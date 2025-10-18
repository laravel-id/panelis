<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Actions\Translation\Backup;
use App\Actions\Translation\Import;
use App\Actions\Translation\ImportFromFiles;
use App\Actions\Translation\Restore;
use App\Filament\Resources\TranslationResource;
use App\Filament\Resources\TranslationResource\Enums\TranslationPermission;
use App\Filament\Resources\TranslationResource\Forms\ExportForm;
use App\Filament\Resources\TranslationResource\Forms\ImportForm;
use App\Models\Translation;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    private static string $disk = 'local';

    protected function authorizeAccess(): void
    {
        abort_unless(user_can(TranslationPermission::Browse), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(user_can(TranslationPermission::Add)),

            ActionGroup::make([
                Action::make('import_files')
                    ->label(__('translation.btn.import_files'))
                    ->icon('heroicon-o-clipboard-document')
                    ->schema(TranslationResource\Forms\ImportFromFileForm::schema())
                    ->action(function (array $data): void {
                        ImportFromFiles::run($data['files'] ?? null);

                        Notification::make('file_imported')
                            ->title(__('filament-actions::create.single.notifications.created.title'))
                            ->success()
                            ->send();
                    }),

                Action::make('import')
                    ->visible(user_can(TranslationPermission::Import))
                    ->label(__('ui.btn.import'))
                    ->icon('heroicon-s-arrow-down-tray')
                    ->modalWidth(Width::Medium)
                    ->modalSubmitActionLabel(__('ui.btn.import'))
                    ->modalDescription(__('translation.import_description'))
                    ->modalIcon('heroicon-o-arrow-up-on-square')
                    ->schema(ImportForm::schema())
                    ->action(function (array $data): void {
                        $lines = json_decode($data['trans']->getContent(), associative: true);
                        try {
                            Import::run($lines, $data['locale']);

                            Notification::make()
                                ->success()
                                ->title(__('translation.file_imported'))
                                ->send();
                        } catch (Exception $e) {
                            Log::error($e);

                            Notification::make()
                                ->danger()
                                ->title(__('translation.import_failed'))
                                ->body(__('translation.import_file_invalid'))
                                ->persistent()
                                ->actions([
                                    Action::make('view')
                                        ->label(__('translation.view_template'))
                                        ->action(function (): ?StreamedResponse {
                                            return response()->streamDownload(function () {
                                                $format = [
                                                    'group.key' => [
                                                        'text' => 'Sample text',
                                                        'is_system' => false,
                                                    ],
                                                ];

                                                echo json_encode($format);
                                            }, 'localization-example.json');
                                        }),
                                ])
                                ->send();
                        }
                    }),

                Action::make('export')
                    ->visible(user_can(TranslationPermission::Export))
                    ->label(__('ui.btn.export'))
                    ->icon('heroicon-s-arrow-up-tray')
                    ->modalWidth(Width::Medium)
                    ->modalDescription(__('translation.export_description'))
                    ->modalIcon('heroicon-o-arrow-down-on-square')
                    ->modalSubmitActionLabel(__('ui.btn.export'))
                    ->schema(ExportForm::schema())
                    ->action(function (array $data): ?StreamedResponse {
                        try {
                            $locale = $data['locale'];
                            $isSystem = $data['is_system'] ?? false;
                            $groups = $data['groups'];

                            if (empty($locale)) {
                                return null;
                            }

                            Notification::make()
                                ->success()
                                ->title(__('translation.export_success'))
                                ->send();

                            return response()->streamDownload(function () use ($locale, $groups, $isSystem): void {
                                echo json_encode(Translation::getFormattedTranslation($locale, $groups, $isSystem));
                            }, sprintf('%s.json', $locale));
                        } catch (Exception $e) {
                            Log::error($e);

                            Notification::make()
                                ->danger()
                                ->title(__('translation.export_failed'))
                                ->send();
                        }

                        return null;
                    }),

                Action::make('backup')
                    ->visible(user_can(TranslationPermission::Backup))
                    ->label(__('ui.btn.backup'))
                    ->icon('heroicon-o-arrow-down-on-square-stack')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        try {
                            Backup::run();

                            Notification::make('backup_success')
                                ->title(__('translation.backup_success'))
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Log::error($e);

                            Notification::make('backup_failed')
                                ->title(__('translation.backup_failed'))
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('restore')
                    ->visible(user_can(TranslationPermission::Restore))
                    ->label(__('ui.btn.restore'))
                    ->icon('heroicon-o-arrow-up-on-square-stack')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        try {
                            Restore::run();

                            Notification::make('restore_success')
                                ->title(__('translation.restore_success'))
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Log::error($e);

                            Notification::make('restore_failed')
                                ->title(__('translation.restore_failed'))
                                ->danger()
                                ->send();
                        }
                    }),
            ]),
        ];
    }
}
