<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
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
            get: function (?string $value): string|array {
                try {
                    if (! empty($value) && config('setting.encrypt_value')) {
                        $value = Crypt::decryptString($value);
                        if (Str::isJson($value)) {
                            return json_decode($value, true);
                        }

                        return $value;
                    }
                } catch (DecryptException $e) {
                    Log::error($e);
                }

                return '';
            },

            set: function (mixed $value): string {
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                if ($value !== '' && config('setting.encrypt_value')) {
                    return Crypt::encryptString($value);
                }

                return '';
            },
        );
    }

    public static function getAll(): Collection
    {
        return Cache::rememberForever(config('setting.cache_key'), function (): Collection {
            return self::query()
                ->select()
                ->fromSub(function (Builder $builder): Builder {
                    return $builder->from('settings')
                        ->selectRaw('ROW_NUMBER() OVER (PARTITION BY key ORDER BY COALESCE(user_id, \'\')) AS row')
                        ->addSelect('user_id', 'key', 'value')
                        ->orderBy('key');
                }, 'subquery')
                ->whereRaw('row = 1')
                ->get();
        });
    }

    public static function getByKey(string $key, mixed $default = null): mixed
    {
        $cachedValue = Cache::get(config('setting.cache_key'));

        if (! empty($cachedValue)) {
            return $cachedValue->where('key', $key)
                ->first()
                ?->value;
        } else {
            return self::query()
                ->where('key', trim($key))
                ->first()
                ?->value ?? $default;
        }
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
