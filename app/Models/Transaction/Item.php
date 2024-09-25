<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $quantity
 * @property float $price
 * @property float $discount
 * @property float $total
 */
class Item extends Model
{
    use HasFactory;

    protected $table = 'transaction_items';

    protected $fillable = [
        'name',
        'quantity',
        'discount',
        'price',
        'total',
    ];

    protected $attributes = [
        'quantity' => 1,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            $model->total = ($model->quantity * $model->price) - $model->discount;
        });
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
