<?php

namespace App\Models\Event;

use App\Filament\Clusters\Databases\Enums\DatabaseType;
use App\Models\Location\District;
use App\Models\Report;
use App\Models\Schedule\Bookmark;
use App\Models\Traits\HasLocalTime;
use App\Models\URL\ShortURL;
use App\Models\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
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
 * @property null|Carbon $finished_at
 * @property Collection $organizers
 * @property ?string $external_url
 * @property Collection $packages
 * @property Collection $types
 * @property string $title
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
        'alias', // title, but in ASCII
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

    public function getOpengraphImageAttribute(): ?string
    {
        return \App\Facades\Schedule::getImage($this);
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

        return $location;
    }

    public function getExternalUrlAttribute(): ?string
    {
        $url = ShortURL::query()
            ->where('destination_url', $this->url)
            ->first();

        if (! empty($url)) {
            return $url->default_short_url;
        }

        return null;
    }

    public function getHeldAtAttribute(): string
    {
        $format = config('app.datetime_format', 'd M Y H:i');
        $timezone = get_timezone();

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

    public function getIsPastAttribute(): bool
    {
        $now = now(get_timezone());

        if (! empty($this->finished_at)) {
            return $this->finished_at
                ->timezone(get_timezone())
                ->lt($now);
        }

        return $this->started_at
            ->timezone(get_timezone())
            ->lt($now);
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
        return $this->hasMany(Package::class)
            ->orderBy('sort');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public static function getPublishedSchedules(?array $filters = null): Collection
    {
        $method = __METHOD__;

        $timezone = get_timezone();

        $date = null;
        if (! empty($filters['date'])) {
            try {
                $date = Carbon::parse($filters['date']);
            } catch (InvalidFormatException) {
            }
        }

        return self::query()
            ->with([
                'district' => fn (BelongsTo $builder): BelongsTo => $builder->select('id', 'name'),
            ])
            ->when(config('database.default') !== DatabaseType::SQLite->value, function () use ($method): void {
                Log::warning('You need to set up custom filter & selector for this query.', [
                    'method' => $method,
                ]);
            })
            ->when(config('database.default') === DatabaseType::SQLite->value, function (Builder $builder): Builder {
                $offset = timezone_offset_get(timezone_open(get_timezone()), now()) / 60 / 60;
                $modifier = vsprintf('%s%s hours', [
                    $offset >= 0 ? '+' : '-',
                    abs($offset),
                ]);

                return $builder->selectRaw(<<<'SELECT'
                    slug,
                    title,
                    location,
                    categories,
                    district_id,
                    started_at,
                    is_virtual,
                    DATE(started_at, ?) AS local_started_at
                SELECT, [$modifier]);
            })
            ->when(! empty($filters['keyword']), function (Builder $builder) use ($filters): Builder {
                $keyword = sprintf('%%%s%%', trim($filters['keyword']));

                return $builder->where(function (Builder $builder) use ($keyword): Builder {
                    return $builder->whereAny(['slug', 'title', 'alias', 'description', 'location', 'categories'], 'LIKE', $keyword)
                        ->orWhereRelation('types', 'title', 'LIKE', $keyword)
                        ->orWhereRelation('organizers', 'name', 'LIKE', $keyword)
                        ->orWhereRelation('organizers', 'slug', 'LIKE', $keyword)
                        ->orWhereRelation('district', 'name', 'LIKE', $keyword);
                });
            })

            // exclude from filters
            ->when(! empty($filters['excludes']), function (Builder $builder) use ($filters): Builder {
                foreach ($filters['excludes'] as $column => $values) {
                    $builder->whereNotIn($column, $values);
                }

                return $builder;
            })

            // do not include virtual event by default
            ->when(empty($filters['virtual']), fn (Builder $builder): Builder => $builder->where('is_virtual', false))

            // filter if 'date' exists
            ->when(! empty($date), fn (Builder $builder): Builder => $builder->where('local_started_at', $date->toDateString()))

            // filter by default date
            ->when(empty($date), function (Builder $builder) use ($timezone, $filters): Builder {
                $now = now($timezone);

                return $builder
                    ->when(empty($filters['past']), fn (Builder $builder): Builder => $builder->whereDate('local_started_at', '>=', $now))
                    ->whereDate('local_started_at', '<=', $now->addYear());
            })
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
        $method = __METHOD__;

        return self::query()
            ->when(config('database.default') !== DatabaseType::SQLite->value, function () use ($method): void {
                Log::warning('You need to set up custom filter & selector for this query.', [
                    'method' => $method,
                ]);
            })
            ->when(config('database.default') === DatabaseType::SQLite->value, function (Builder $builder): Builder {
                $offset = timezone_offset_get(timezone_open(get_timezone()), now()) / 60 / 60;
                $modifier = vsprintf('%s%s hours', [
                    $offset >= 0 ? '+' : '-',
                    abs($offset),
                ]);

                return $builder->selectRaw('*, DATE(started_at, ?) AS local_started_at', [$modifier]);
            })
            ->when(! empty($month), function (Builder $builder) use ($year, $month): Builder {
                $local = now(get_timezone())
                    ->setMonth($month)
                    ->setYear($year);

                if (config('database.default') === DatabaseType::SQLite->value) {
                    return $builder->whereBetween('local_started_at', [
                        $local->startOfMonth()->toDateString(),
                        $local->endOfMonth()->toDateString(),
                    ]);
                }

                return $builder;
            })
            ->whereYear('started_at', $year)
            ->orderBy('started_at')
            ->with(['district'])
            ->get();
    }

    public static function getArchivedSchedules(): Collection
    {
        $offset = timezone_offset_get(timezone_open(get_timezone()), now()) / 60 / 60;
        $modifier = vsprintf('%s%s hours', [
            $offset >= 0 ? '+' : '-',
            abs($offset),
        ]);

        return self::query()
            ->selectRaw(<<<'SELECT'
                    slug,
                    title,
                    location,
                    categories,
                    district_id,
                    started_at,
                    DATE(started_at, ?) AS local_started_at
                SELECT, [$modifier])
            ->with([
                'district' => fn (BelongsTo $builder): BelongsTo => $builder->select('id', 'name'),
            ])
            ->orderByDesc('started_at')
            ->whereDate('local_started_at', '<=', now(get_timezone()))
            ->get();
    }

    public function toSitemapTag(): Url|string|array
    {
        return Url::create(route('schedule.view', $this->slug))
            ->addImage($this->opengraph_image);
    }
}
