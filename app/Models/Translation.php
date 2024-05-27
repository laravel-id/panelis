<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Spatie\TranslationLoader\LanguageLine;

class Translation extends LanguageLine
{
    public $table = 'language_lines';

    protected $casts = [
        'text' => 'array',
        'is_system' => 'boolean',
    ];

    public static function getFormattedTranslation(string $locale, bool $isSystem = false): ?array
    {
        return Translation::orderBy('group')
            ->orderBy('key')
            ->when($isSystem, fn (Builder $builder): Builder => $builder->where('is_system', true))
            ->get()
            ->mapWithKeys(function ($line) use ($locale): array {
                $key = sprintf('%s.%s', $line->group, $line->key);
                $text = data_get($line->text, $locale);

                return [$key => [
                    'text' => $text,
                    'is_system' => $line->is_system,
                ]];
            })
            ->toArray();
    }
}
