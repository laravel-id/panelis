<?php

namespace App\Models;

use App\Filament\Resources\SubscriberResource\Enums\SubscriberPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property Carbon $subscribed_at
 * @property SubscriberPeriod $period
 * @property string $email
 * @property string $confirmation_key
 * @property bool $is_subscribed
 */
class Subscriber extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'confirmation_key',
        'email',
        'subscribed_at',
        'period',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'period' => SubscriberPeriod::class,
    ];

    public function getIsSubscribedAttribute(): bool
    {
        return ! empty($this->subscribed_at);
    }

    public function scopeSubscribed(Builder $builder, bool $status = true): Builder
    {
        if ($status) {
            return $builder->whereNotNull('subscribed_at');
        }

        return $builder->whereNull('subscribed_at');
    }

    public static function getPeriods(): array
    {
        return SubscriberPeriod::options();
    }

    public function unsubscribe(): bool
    {
        $this->fill([
            'confirmation_key' => Str::random(40),
            'subscribed_at' => null,
        ]);

        return $this->save();
    }

    public function subscribe(): bool
    {
        $this->fill([
            'confirmation_key' => Str::random(40),
            'subscribed_at' => now(),
        ]);

        return $this->save();
    }

    public static function getActiveSubscribers(?SubscriberPeriod $period = null): Collection
    {
        return self::query()
            ->when(! empty($period), fn (Builder $builder): Builder => $builder->where('period', $period))
            ->whereNotNull('subscribed_at')
            ->get();
    }
}
