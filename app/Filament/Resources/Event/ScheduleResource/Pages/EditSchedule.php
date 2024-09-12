<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Events\Event\ScheduleUpdated;
use App\Filament\Resources\Event\ScheduleResource;
use App\Models\URL\ShortURL;
use AshAllenDesign\ShortURL\Exceptions\ShortURLException;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

        if (! Str::isAscii($data['title'])) {
            $data['alias'] = Str::ascii($data['title']);
        }

        return $data;
    }

    /**
     * @throws ShortURLException
     */
    protected function afterSave(): void
    {
        event(new ScheduleUpdated($this->record));

        // clear cached response
        Cache::forget('response.'.sha1(route('schedule.view', $this->record->slug)));

        // clear pinned event
        Cache::forget('event.pinned');

        $exists = ShortURL::query()
            ->where('destination_url', $this->record->url)
            ->exists();

        if (! $exists) {
            URLShortener::destinationUrl($this->record->url)
                ->redirectStatusCode(302)
                ->trackVisits()
                ->make();
        }
    }
}
