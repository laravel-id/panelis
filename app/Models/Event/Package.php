<?php

namespace App\Models\Event;

use App\Filament\Resources\Event\ScheduleResource\Enums\PackagePriceType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float $price
 * @property PackagePriceType $price_type
 * @property bool $is_sold
 * @property Carbon $started_at
 * @property Carbon $ended_at
 * @property string $description
 * @property string $url
 * @property bool $is_past
 */
class Package extends Model
{
    use HasFactory;

    protected $casts = [
        'sort' => 'int',
        'price' => 'float',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'price_type' => PackagePriceType::class,
        'is_sold' => 'bool',
    ];

    protected $fillable = [
        'sort',
        'title',
        'price_type',
        'price',
        'is_sold',
        'started_at',
        'ended_at',
        'description',
        'url',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * @return HasMany<Participant>
     */
    public function participants(): HasMany
    {
        return $this->hasmany(Participant::class);
    }

    /**
     * @return BelongsTo<Payment>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
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

    public function isPast(): Attribute
    {
        return new Attribute(
            get: function (): bool {
                if (! empty($this->ended_at)) {
                    $timezone = get_timezone();

                    return $this->ended_at->timezone($timezone)->lt(now($timezone));
                }

                return false;
            }
        );
    }
}
