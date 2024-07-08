<?php

namespace App\Actions\User;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class BackupPermission
{
    use AsAction;

    public function handle(): string
    {
        abort_if(! Auth::user()->can('BackupPermission'), Response::HTTP_FORBIDDEN);

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
