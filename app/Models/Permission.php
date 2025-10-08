<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string $label
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;

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
