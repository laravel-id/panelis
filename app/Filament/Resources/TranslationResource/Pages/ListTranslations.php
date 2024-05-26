<?php

namespace App\Filament\Resources\TranslationResource\Pages;

use App\Filament\Resources\TranslationResource;
use App\Models\Translation;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\ListRecords;
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
                    foreach ($lines as $index => $line) {
                        [$group, $key] = explode('.', $index, 2);

                        $trans = Translation::firstOrNew([
                            'group' => $group,
                            'key' => $key,
                        ]);

                        $newLine = [$data['locale'] => $line];
                        if (! empty($trans->text)) {
                            $trans->text = array_merge($trans->text, $newLine);
                        } else {
                            $trans->text = $newLine;
                        }
                        $trans->save();
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
                ])
                ->action(function (array $data): ?StreamedResponse {
                    $locale = $data['locale'];
                    if (empty($locale)) {
                        return null;
                    }

                    return response()->streamDownload(function () use ($locale): void {
                        echo json_encode(Translation::getFormattedTranslation($locale));
                    }, sprintf('%s.json', $locale));
                }),

            ActionGroup::make([
                Actions\CreateAction::make(),
            ]),
        ];
    }
}
