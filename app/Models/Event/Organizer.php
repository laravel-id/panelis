<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

/**
 * @property string $description
 * @property string $logo
 * @property string $name
 * @property string $slug
 */
class Organizer extends Model implements Sitemapable
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'phone',
        'email',
        'website',
        'address',
        'color',
    ];

    public function logo(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?string {
                if (empty($value)) {
                    $options = [
                        'name' => $this->name,
                        'background' => $this->color ?? 'random',
                    ];

                    return sprintf('https://ui-avatars.com/api?%s', http_build_query($options));
                }

                return $value;
            },
        );
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class);
    }

    public static function options(): array
    {
        return self::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function toSitemapTag(): Url|string|array
    {
        return route('organizer.view', $this->slug);
    }
}
