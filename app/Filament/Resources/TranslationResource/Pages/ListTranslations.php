<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Filament\Resources\TranslationResource;
use App\Models\Translation;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Exception;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    private static string $disk = 'local';

    private function import(?array $lines, string $locale): void
    {
        foreach ($lines as $index => $line) {
            [$group, $key] = explode('.', $index, 2);

            $trans = Translation::query()
                ->firstOrNew([
                    'group' => $group,
                    'key' => $key,
                ]);

            $newLine = [$locale => $line['text']];

            $trans->is_system = $line['is_system'];
            if (! empty($trans->text)) {
                $trans->text = array_merge($trans->text, $newLine);
            } else {
                $trans->text = $newLine;
            }
            $trans->save();
        }
    }

    protected function getHeaderActions(): array
    {
        $locales = collect(config('app.locales'))
            ->mapWithKeys(function ($locale): array {
                return [$locale => LanguageSwitch::make()->getLabel($locale)];
            })
            ->toArray();

        return [
            Action::make('import')
                ->label(__('translation.import'))
                ->modalWidth(MaxWidth::Medium)
                ->modalSubmitActionLabel(__('translation.import_submit'))
                ->modalDescription(__('translation.import_description'))
                ->modalIcon('heroicon-o-arrow-up-on-square')
                ->color('warning')
                ->form([
                    Radio::make('locale')
                        ->required()
                        ->label(__('translation.locale'))
                        ->live()
                        ->options($locales),

                    FileUpload::make('trans')
                        ->previewable(false)
                        ->storeFiles(false)
                        ->fetchFileInformation(false)
                        ->disk(self::$disk)
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/json',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $lines = json_decode($data['trans']->getContent(), associative: true);
                    try {
                        $this->import($lines, $data['locale']);

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
                ->label(__('translation.export'))
                ->modalWidth(MaxWidth::Medium)
                ->modalDescription(__('translation.export_description'))
                ->modalIcon('heroicon-o-arrow-down-on-square')
                ->modalSubmitActionLabel(__('translation.export_submit'))
                ->form([
                    Radio::make('locale')
                        ->label(__('translation.locale'))
                        ->required()
                        ->options($locales),

                    Toggle::make('is_system')
                        ->inline(false)
                        ->label(__('translation.system_only')),
                ])
                ->action(function (array $data): ?StreamedResponse {
                    try {
                        $locale = $data['locale'];
                        $isSystem = $data['is_system'] ?? false;

                        if (empty($locale)) {
                            return null;
                        }

                        Notification::make()
                            ->success()
                            ->title(__('translation.export_success'))
                            ->send();

                        return response()->streamDownload(function () use ($locale, $isSystem): void {
                            echo json_encode(Translation::getFormattedTranslation($locale, $isSystem));
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

            ActionGroup::make([
                Actions\CreateAction::make(),

                Action::make('backup')
                    ->label(__('translation.backup'))
                    ->icon('heroicon-o-arrow-down-on-square-stack')
                    ->requiresConfirmation()
                    ->modalDescription(__('translation.backup_confirmation'))
                    ->action(function (): void {
                        try {
                            foreach (LanguageSwitch::make()->getLocales() as $locale) {
                                $content = Translation::getFormattedTranslation($locale);

                                Storage::disk(self::$disk)
                                    ->put(sprintf('locales/%s.json', $locale), json_encode($content));
                            }

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
                    ->label(__('translation.restore'))
                    ->icon('heroicon-o-arrow-up-on-square-stack')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        try {
                            $files = Storage::disk(self::$disk)->allFiles('locales');
                            foreach ($files as $file) {
                                [$locale, $ext] = explode('.', basename($file), 2);
                                unset($ext);

                                $content = Storage::disk(self::$disk)->get($file);
                                $this->import(json_decode($content, associative: true), $locale);
                            }

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
