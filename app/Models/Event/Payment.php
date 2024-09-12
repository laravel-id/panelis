<?php

namespace App\Models\Event;

use App\Enums\Events\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property PaymentStatus $status
 */
class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'vendor',
        'quantity',
        'price',
        'discount',
        'total',
        'status',
        'expired_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'expired_at' => 'datetime',
    ];

    /**
     * @return HasOne<Package>
     */
    public function package(): HasOne
    {
        return $this->hasOne(Package::class);
    }
}
