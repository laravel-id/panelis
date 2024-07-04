<?php

namespace App\Actions\User;

use App\Models\Permission;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class SeedPermission
{
    use AsAction;

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        $path = database_path('seeds/permission.json');
        throw_if(! File::exists($path), 'Permission file (JSON) does not exist.');

        $permissions = File::get($path);
        throw_if(! Str::isJson($permissions), 'Permission file contains invalid JSON format.');

        foreach (json_decode($permissions, true) as $permission) {
            $key = Str::snake($permission['name']);

            Permission::query()->updateOrCreate(['name' => $permission['name']], [
                'guard_name' => $permission['guard'],
                'label' => "user.permission_label_{$key}",
                'description' => "user.permission_description_{$key}",
                'is_default' => true,
            ]);
        }
    }
}
