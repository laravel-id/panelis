<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Filament\Resources\Event\ScheduleResource;
use App\Models\URL\ShortURL;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['description'])) {
            $data['description'] = '';
        }

        $exists = ShortURL::query()
            ->where('destination_url', $data['url'])
            ->exists();

        if (! $exists) {
            URLShortener::destinationUrl($data['url'])
                ->trackVisits()
                ->make();
        }

        return $data;
    }
}
