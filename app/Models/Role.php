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
 * @property bool $is_admin
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;
    use HasLocalTime;

    public static function options(): array
    {
        return self::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (Role $role): array {
                $label = $role->name;

                if ($role->is_admin) {
                    $label .= sprintf(' (%s)', __('user.role_admin_access'));
                }

                return [$role->id => $label];
            })
            ->toArray();
    }
}
