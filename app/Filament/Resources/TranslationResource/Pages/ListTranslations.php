<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Filament\Resources\TranslationResource;
use App\Models\Translation;
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
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label(__('translation.import'))
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-arrow-up-on-square')
                ->form([
                    Radio::make('locale')
                        ->required()
                        ->label(__('translation.locale'))
                        ->live()
                        ->options([
                            'en' => __('translation.locale_en'),
                            'id' => __('translation.locale_id'),
                        ]),

                    FileUpload::make('trans')
                        ->previewable(false)
                        ->storeFiles(false)
                        ->fetchFileInformation(false)
                        ->disk('local')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/json',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $lines = json_decode($data['trans']->getContent(), associative: true);
                    try {
                        foreach ($lines as $index => $line) {
                            [$group, $key] = explode('.', $index, 2);

                            $trans = Translation::firstOrNew([
                                'group' => $group,
                                'key' => $key,
                            ]);

                            $newLine = [$data['locale'] => $line['text']];

                            $trans->is_system = $line['is_system'];
                            if (! empty($trans->text)) {
                                $trans->text = array_merge($trans->text, $newLine);
                            } else {
                                $trans->text = $newLine;
                            }
                            $trans->save();
                        }

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
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-arrow-down-on-square')
                ->color('success')
                ->form([
                    Radio::make('locale')
                        ->label(__('translation.locale'))
                        ->required()
                        ->options([
                            'en' => __('translation.locale_en'),
                            'id' => __('translation.locale_id'),
                        ]),

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
            ]),
        ];
    }
}
