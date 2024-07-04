<?php

namespace App\Models;

use App\Models\Traits\HasLocalTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static pluck(string $value, string $label)
 *
 * @property bool $is_default
 * @property string $label
 * @property string $description
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;
    use HasLocalTime;

    public function label(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => __($value),
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => __($value),
        );
    }
}
