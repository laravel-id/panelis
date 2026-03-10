<?php

namespace Modules\Translation\Actions;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Translation\Models\Translation;

class Backup
{
    use AsAction;

    private static string $disk = 'local';

    public function handle(): void
    {
        foreach (LanguageSwitch::make()->getLocales() as $locale) {
            $content = Translation::getFormattedTranslation($locale);

            Storage::disk(self::$disk)
                ->put(sprintf('locales/%s.json', $locale), json_encode($content));
        }
    }
}
