<?php

namespace App\Models;

use App\Models\Traits\HasLocalTime;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\TranslationLoader\LanguageLine;

/**
 * @method static orderBy(string $sort, ?string $order = null)
 *
 * @property bool $is_system
 */
class Translation extends LanguageLine
{
    use HasLocalTime;

    public $table = 'language_lines';

    protected $casts = [
        'text' => 'array',
        'is_system' => 'boolean',
    ];

    public function group(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => strtolower($value),
        );
    }

    public function key(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => strtolower($value),
        );
    }

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
