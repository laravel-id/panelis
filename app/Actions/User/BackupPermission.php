<?php

namespace App\Actions\User;

use App\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class BackupPermission
{
    use AsAction;

    public function handle(): string
    {
        $permissions = Permission::query()
            ->get()
            ->mapWithKeys(function (Permission $permission): array {
                return [$permission->name => [
                    'name' => $permission->name,
                    'guard' => $permission->guard_name,
                ]];
            })
            ->toJson(JSON_PRETTY_PRINT);

        Storage::put($path = 'users/permission.json', $permissions);

        return Storage::path($path);
    }
}
