<?php

namespace App\Models\Event;

use App\Models\Location\District;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
 */
class Schedule extends Model
{
    use HasFactory;
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
        return route('schedule.go', ['url' => $this->url]);
    }

    public function getHeldAtAttribute(): string
    {
        $format = config('app.datetime_format', 'd M Y H:i');

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

    public static function getPublishedSchedules(array $request): Collection
    {
        $timezone = config('app.datetime_timezone', config('app.timezone'));

        return self::query()
            ->with(['district'])
            ->when(! empty($request['keyword']), function ($builder) use ($request) {
                $builder->whereAny(['title', 'description', 'location'], 'LIKE', '%'.$request['keyword'].'%')
                    ->orWhereRelation('organizers', 'name', 'LIKE', '%'.$request['keyword'].'%')
                    ->orWhereRelation('organizers', 'slug', 'LIKE', '%'.$request['keyword'].'%')
                    ->orWhereRelation('district', 'name', 'LIKE', '%'.$request['keyword'].'%');
            })
            ->where('started_at', '>=', now($timezone)->toDateString())
            ->orderBy('started_at')
            ->get();
    }

    public static function getScheduleByYearAndSlug(int $year, string $slug): Model
    {
        return self::query()
            ->whereYear('started_at', $year)
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
}
