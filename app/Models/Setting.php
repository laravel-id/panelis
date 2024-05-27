<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public function value(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): string {
                try {
                    if (! empty($value) && config('setting.encrypt_value')) {
                        return Crypt::decryptString($value);
                    }
                } catch (DecryptException) {
                }

                return '';
            },

            set: function (?string $value): string {
                if (! empty($value) && config('setting.encrypt_value')) {
                    return Crypt::encryptString($value);
                }

                return '';
            },
        );
    }

    public static function getByKey(string $key, mixed $default = null): mixed
    {
        $cachedValue = Cache::get(config('setting.cache_key'))
            ->where('key', $key)
            ->first();

        if (! empty($cachedValue)) {
            return $cachedValue->value;
        } else {
            return self::query()
                ->where('key', trim($key))
                ->first()
                ?->value ?? $default;
        }

        return null;
    }
}
