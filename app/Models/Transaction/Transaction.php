<?php

namespace App\Models\Transaction;

use App\Enums\Transaction\TransactionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @property string $ulid
 * @property float $total
 * @property array $metadata
 * @property Bank $bank
 * @property Item $items
 * @property int|null $user_id
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_id',
        'ulid',
        'vendor_id',
        'vendor',
        'transactionable_id',
        'transactionable_type',
        'total',
        'status',
        'metadata',
        'expired_at',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'expired_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'status' => TransactionStatus::Pending,
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model): void {
            $model->user_id = Auth::id();
            $model->ulid = Str::ulid();
        });
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
