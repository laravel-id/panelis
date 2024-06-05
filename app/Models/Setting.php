<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @method static updateOrCreate(array $keys, array $data)
 */
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
            get: function (?string $value): null|string|array {
                try {
                    if (config('setting.encrypt_value')) {
                        $value = Crypt::decryptString($value);
                    }

                    $value = unserialize($value);

                    if (Str::isJson($value)) {
                        return json_decode($value, true);
                    }

                    return $value;
                } catch (DecryptException $e) {
                    Log::error($e);
                }

                return unserialize($value);
            },

            set: function (mixed $value): string {
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                if ($value !== '' && config('setting.encrypt_value')) {
                    return Crypt::encryptString(serialize($value));
                }

                return serialize($value);
            },
        );
    }

    public static function getAll(): Collection
    {
        if (config('setting.cache') && Cache::has(config('setting.cache_key'))) {
            return Cache::get(config('setting.cache_key'));
        }

        $settings = self::query()
            ->select('key', 'value')
            ->whereNull('user_id')
            ->get();

        if (config('setting.cache')) {
            Cache::put(config('setting.cache_key'), $settings);
        }

        return $settings;
    }

    public static function getByUser(int $userId): Collection
    {
        return self::query()
            ->select('key', 'value')
            ->whereUserId($userId)
            ->get();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $cachedValue = Cache::get(config('setting.cache_key'));

        if (! empty($cachedValue)) {
            return $cachedValue->where('key', $key)
                ->first()
                ?->value ?? $default;
        } else {
            return self::query()
                ->where('key', trim($key))
                ->first()
                ?->value ?? $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(compact('key'), compact('value'));
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
