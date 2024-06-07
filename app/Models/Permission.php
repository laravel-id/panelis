<?php

namespace App\Models;

use App\Models\Traits\HasLocalTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static pluck(string $value, string $label)
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;
    use HasLocalTime;
}
