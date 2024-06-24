<?php

namespace App\Models\Event;

use App\Models\Location\District;
use App\Models\ShortURL;
use App\Models\Traits\HasLocalTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

/**
 * @property array $metadata
 * @property string $location
 * @property string $full_location
 * @property string $description
 * @property int $id
 * @property bool $is_virtual
 * @property string $url
 * @property string $slug
 * @property Carbon $started_at
 * @property Collection $organizers
 * @property ?string $external_url
 */
class Schedule extends Model implements Sitemapable
{
    use HasFactory;
    use HasLocalTime;
    use SoftDeletes;

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'metadata' => 'array',
        'contacts' => 'array',
        'categories' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'district_id',
        'slug',
        'title',
        'description',
        'categories',
        'poster',
        'started_at',
        'finished_at',
        'is_virtual',
        'location',
        'contacts',
        'url',
        'metadata',
    ];

    public function contacts(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): array {
                return collect(json_decode($value, true))
                    ->map(function (array $contact): array {
                        if (! empty($contact['is_wa']) && $contact['is_wa'] === true && ! empty($contact['phone'])) {
                            $phone = $contact['phone'];

                            // assume it's local number
                            if (str_starts_with($phone, '0')) {
                                $phone = ltrim($phone, '0');
                                $phone = sprintf('%s%s', '62', $phone);
                            }

                            // provider country number
                            if (str_starts_with($phone, '+')) {
                                $phone = str_replace('+', '', $phone);
                            }

                            $contact['wa_url'] = sprintf('https://wa.me/%s', $phone);
                        }

                        return $contact;
                    })
                    ->toArray();
            },
        );
    }

    public function getStartTimeAttribute(): string
    {
        return $this->started_at->format('H:i');
    }

    public function getFinishTimeAttribute(): ?string
    {
        if (! empty($this->finished_at)) {
            return $this->finished_at->format('H:i');
        }

        return null;
    }

    public function getFullLocationAttribute(): ?string
    {
        $location = $this->location;

        if (! empty($this->district)) {
            $location = sprintf('%s, %s', $this->location, $this->district->name);
        }

        if (! empty($this->metadata['location_url'])) {
            return Str::of(sprintf('[%s](%s)', $location, $this->metadata['location_url']))
                ->inlineMarkdown()
                ->toHtmlString();
        }

        return $location;
    }

    public function getExternalUrlAttribute(): string
    {
        $url = ShortURL::query()
            ->where('destination_url', $this->url)
            ->first();

        if (! empty($url)) {
            return $url->default_short_url;
        }

        return route('schedule.go', ['url' => $this->url]);
    }

    public function getHeldAtAttribute(): string
    {
        $format = config('app.datetime_format', 'd M Y H:i');
        $timezone = config('app.datetime_timezone', config('app.timezone'));

        $this->started_at = $this->started_at->timezone($timezone);
        if (! empty($this->finished_at)) {
            $this->finished_at = $this->finished_at->timezone($timezone);
        }

        if (! empty($this->finished_at)) {
            if ($this->started_at->isSameDay($this->finished_at)) {
                return vsprintf('%s - %s', [
                    $this->started_at->translatedFormat($format),
                    $this->finished_at->translatedFormat('H:i'),
                ]);
            }

            return vsprintf('%s - %s', [
                $this->started_at->translatedFormat($format),
                $this->finished_at->translatedFormat($format),
            ]);
        }

        return $this->started_at->translatedFormat($format);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organizers(): BelongsToMany
    {
        return $this->belongsToMany(Organizer::class);
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public static function getPublishedSchedules(?array $request = null): Collection
    {
        $timezone = config('app.datetime_timezone', config('app.timezone'));
        $now = now($timezone);

        return self::query()
            ->with(['district'])
            ->when(! empty($request['keyword']), function ($builder) use ($request) {
                $keyword = sprintf('%%%s%%', $request['keyword']);

                $builder->whereAny(['title', 'description', 'location', 'categories'], 'LIKE', $keyword)
                    ->orWhereRelation('types', 'title', 'LIKE', $keyword)
                    ->orWhereRelation('organizers', 'name', 'LIKE', $keyword)
                    ->orWhereRelation('organizers', 'slug', 'LIKE', $keyword)
                    ->orWhereRelation('district', 'name', 'LIKE', $keyword);
            })
            ->whereDate('started_at', '>=', $now)
            ->whereDate('started_at', '<=', $now->addYear())
            ->orderBy('started_at')
            ->get();
    }

    public static function getScheduleBySlug(string $slug): Model
    {
        return self::query()
            ->where('slug', $slug)
            ->with(['packages'])
            ->firstOrFail();
    }

    public static function getFilteredSchedules(int $year, ?int $month = null): Collection
    {
        return self::query()
            ->when(! empty($month), fn ($builder) => $builder->whereMonth('started_at', $month))
            ->whereYear('started_at', $year)
            ->orderBy('started_at')
            ->with(['district'])
            ->get();
    }

    public static function getArchivedSchedules(): Collection
    {
        return self::query()
            ->orderByDesc('started_at')
            ->whereDate('started_at', '<=', now(
                config('app.datetime_timezone', config('app.timezone'))
            ))
            ->get();
    }

    public function toSitemapTag(): Url|string|array
    {
        return route('schedule.view', $this->slug);
    }
}
