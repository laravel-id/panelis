<?php

namespace App\Filament\Resources\URL\ShortURLResource\Pages;

use App\Filament\Resources\URL\ShortURLResource;
use App\Models\URL\ShortURL;
use AshAllenDesign\ShortURL\Exceptions\ShortURLException;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateShortURL extends CreateRecord
{
    protected static string $resource = ShortURLResource::class;

    /**
     * @throws ShortURLException
     */
    protected function handleRecordCreation(array $data): Model
    {
        $urls = ShortURL::findByDestinationURL($data['destination_url']);
        if ($urls->count() >= 1) {
            return $urls->first();
        }

        $url = URLShortener::destinationUrl($data['destination_url'])
            ->trackVisits($data['track_visits'] ?? true)
            ->singleUse($data['single_use'] ?? false);

        if (! empty($data['deactivated_at'])) {
            $url->deactivateAt(Carbon::parse($data['deactivated_at']));
        }

        if (! empty($data['url_key'])) {
            $url->urlKey($data['url_key']);
        }

        return $url->make();
    }
}
