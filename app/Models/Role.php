<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static pluck(string $value, string $label)
 * @method int count()
 *
 * @property string $description
 * @property int $users_count
 * @property bool $is_admin
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    public static function options(): array
    {
        return self::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (Role $role): array {
                $label = $role->name;

                if ($role->is_admin) {
                    $label .= sprintf(' (%s)', __('user.role.admin_access'));
                }

                return [$role->id => $label];
            })
            ->toArray();
    }
}
