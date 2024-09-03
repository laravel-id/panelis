<?php

namespace App\Models\Event;

use App\Filament\Clusters\Databases\Enums\DatabaseType;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'categories' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'is_virtual' => 'boolean',
    ];

    public function isPast(): Attribute
    {
        return new Attribute(
            get: function (): bool {
                $now = now(get_timezone());

                if (!empty($this->finished_at)) {
                    return $this->finished_at
                        ->timezone(get_timezone())
                        ->lt($now);
                }

                return $this->started_at
                    ->timezone(get_timezone())
                    ->lt($now);
            },
        );
    }

    public function isOngoing(): Attribute
    {
        return new Attribute(
            get: function (): bool {
                $now = now(get_timezone());
                $start = $this->started_at->timezone(get_timezone());
                if (!empty($this->finished_at)) {
                    $finish = $this->finished_at->timezone(get_timezone());
                } else {
                    $finish = $start->copy()->addHours(3);
                }

                return $now->gte($start) && $now->lte($finish);
            }
        );
    }

    public static function getPublishedSchedules(array $filters = []): Collection
    {
        extract($filters); // keyword, virtual, past, date

        $virtual = (bool) $virtual;
        $past = (bool) $past;

        if (!empty($date)) {
            try {
                $date = Carbon::parse($date);
            } catch (InvalidFormatException) {
            }
        }

        $sanitizedKeyword = preg_replace('/[^a-zA-Z0-9\s]/', '', $keyword);
        $sanitizedKeyword .= '*';

        return self::query()
            ->when(config('database.default') === DatabaseType::SQLite->value, function (Builder $builder): Builder {
                $offset = timezone_offset_get(timezone_open(get_timezone()), now()) / 60 / 60;
                $modifier = vsprintf('%s%s hours', [
                    $offset >= 0 ? '+' : '-',
                    abs($offset),
                ]);

                return $builder->selectRaw(<<<'SELECT'
                    slug,
                    title,
                    highlight(events, 2, '<b>', '</b>') marked_title,
                    location,
                    categories,
                    started_at,
                    finished_at,
                    is_virtual,
                    DATE(started_at, ?) AS local_started_at
                SELECT, [$modifier]);
            })
            ->when(!empty($keyword), fn(Builder $builder): Builder => $builder->whereRaw('events MATCH ?', [$sanitizedKeyword]))
            ->when(!$virtual, fn(Builder $builder): Builder => $builder->where('is_virtual', false))
            ->when(empty($date), function (Builder $builder) use ($past): Builder {
                $now = CarbonImmutable::now(get_timezone());

                return $builder
                    ->when(!$past, fn(Builder $builder): Builder => $builder->whereDate('local_started_at', '>=', $now))
                    ->whereDate('local_started_at', '<=', $now->addYear());
            })
            ->orderBy('local_started_at')
            ->orderBy('rank')
            ->get();
    }
}
