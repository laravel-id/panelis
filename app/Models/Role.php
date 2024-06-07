<?php

namespace App\Models;

use App\Models\Traits\HasLocalTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static pluck(string $value, string $label)
 * @method int count()
 *
 * @property string $local_created_at
 * @property string $local_updated_at
 * @property string $description
 * @property int $users_count
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;
    use HasLocalTime;
}
