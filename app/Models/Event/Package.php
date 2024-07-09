<?php

namespace App\Models\Event;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $price
 * @property Carbon $started_at
 * @property Carbon $ended_at
 */
class Package extends Model
{
    use HasFactory;

    protected $casts = [
        'sort' => 'int',
        'price' => 'float',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected $fillable = [
        'sort',
        'title',
        'price',
        'started_at',
        'ended_at',
        'description',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function period(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! empty($this->started_at)) {
                    $started = $this->started_at
                        ->timezone(get_timezone())
                        ->translatedFormat('D, d F Y');
                }

                if (! empty($this->ended_at)) {
                    $ended = $this->ended_at
                        ->timezone(get_timezone())
                        ->translatedFormat('D, d F Y');
                }

                if (! empty($started) && ! empty($ended)) {
                    return sprintf('%s – %s', $started, $ended);
                }

                return $started ?? null;
            },
        );
    }

    public function startedAt(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?Carbon {
                if (! empty($value)) {
                    return Carbon::parse($value)->timezone(get_timezone());
                }

                return null;
            },
        );
    }

    public function endedAt(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?Carbon {
                if (! empty($value)) {
                    return Carbon::parse($value)->timezone(get_timezone());
                }

                return null;
            },
        );
    }
}
