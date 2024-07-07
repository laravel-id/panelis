<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Actions\Translation\Backup;
use App\Actions\Translation\Import;
use App\Actions\Translation\Restore;
use App\Filament\Resources\TranslationResource;
use App\Models\Translation;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Exception;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    private static string $disk = 'local';

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()->can('ViewTranslation'), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(Auth::user()->can('CreateTranslation')),

            ActionGroup::make([
                Action::make('import')
                    ->visible(Auth::user()->can('ImportTranslation'))
                    ->label(__('translation.import'))
                    ->icon('heroicon-s-arrow-down-tray')
                    ->modalWidth(MaxWidth::Medium)
                    ->modalSubmitActionLabel(__('translation.import_submit'))
                    ->modalDescription(__('translation.import_description'))
                    ->modalIcon('heroicon-o-arrow-up-on-square')
                    ->form(TranslationResource\Forms\ImportForm::make())
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
                                    NotificationAction::make('view')
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
                    ->visible(Auth::user()->can('ExportTranslation'))
                    ->label(__('translation.export'))
                    ->icon('heroicon-s-arrow-up-tray')
                    ->modalWidth(MaxWidth::Medium)
                    ->modalDescription(__('translation.export_description'))
                    ->modalIcon('heroicon-o-arrow-down-on-square')
                    ->modalSubmitActionLabel(__('translation.export_submit'))
                    ->form(TranslationResource\Forms\ExportForm::make())
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
                    ->visible(Auth::user()->can('BackupTranslation'))
                    ->label(__('translation.backup'))
                    ->icon('heroicon-o-arrow-down-on-square-stack')
                    ->requiresConfirmation()
                    ->modalDescription(__('translation.backup_confirmation'))
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
                    ->visible(Auth::user()->can('RestoreTranslation'))
                    ->label(__('translation.restore'))
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
