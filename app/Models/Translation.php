<?php

namespace App\Models;

use Spatie\TranslationLoader\LanguageLine;

class Translation extends LanguageLine
{
    public $table = 'language_lines';

    protected $casts = [
        'text' => 'array',
        'is_system' => 'boolean',
    ];

    public static function getFormattedTranslation(string $locale): ?array
    {
        return Translation::get()
            ->mapWithKeys(function ($line) use ($locale): array {
                $key = sprintf('%s.%s', $line->group, $line->key);
                $text = data_get($line->text, $locale);

                return [$key => $text];
            })
            ->toArray();
    }
}
