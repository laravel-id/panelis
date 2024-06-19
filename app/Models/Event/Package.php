<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $price
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
}
