<?php

namespace App\Models\Transaction;

use App\Models\Traits\HasLocalTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $vendor_id
 */
class Bank extends Model
{
    use HasFactory;
    use HasLocalTime;

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

    public static function options(): array
    {
        return self::query()
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(fn (self $bank): array => [$bank->id => sprintf('%s - (%s - %s)', $bank->bank_name, $bank->account_number, $bank->account_name)])
            ->all();
    }
}
