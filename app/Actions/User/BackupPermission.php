<?php

namespace App\Actions\User;

use App\Filament\Resources\PermissionResource\Enums\Permission;
use App\Models\Permission as Model;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class BackupPermission
{
    use AsAction;

    public function handle(): string
    {
        abort_if(! user_can(Permission::Backup), Response::HTTP_FORBIDDEN);

        $permissions = Model::query()
            ->get()
            ->mapWithKeys(function (Model $permission): array {
                return [$permission->name => [
                    'name' => $permission->name,
                    'label' => $permission->label,
                    'guard' => $permission->guard_name,
                ]];
            })
            ->toJson(JSON_PRETTY_PRINT);

        Storage::put($path = 'users/permission.json', $permissions);

        return Storage::path($path);
    }
}
