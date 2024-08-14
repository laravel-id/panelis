<?php

namespace App\Actions\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportAll
{
    use AsAction;

    public function handle(): StreamedResponse
    {
        abort_if(! Auth::user()->can('ExportSetting'), Response::HTTP_FORBIDDEN);

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
