<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $vendor_id
 */
class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'vendor',
        'bank_code',
        'bank_name',
        'account_name',
        'account_number',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasmany(Transaction::class);
    }
}
