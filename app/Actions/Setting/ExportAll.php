<?php

namespace App\Actions\Setting;

use App\Models\Setting;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportAll
{
    use AsAction;

    public function handle(): StreamedResponse
    {
        return response()->streamDownload(function () {
            echo Setting::query()
                ->whereNull('user_id')
                ->orderBy('key')
                ->get()
                ->map(function (Setting $setting): array {
                    return [
                        'key' => $setting->key,
                        'value' => $setting->value,
                    ];
                })
                ->toJson();
        }, strtolower(sprintf('%s-settings.json', config('app.name'))));
    }
}
