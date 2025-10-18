<?php

namespace App\Actions\User;

use App\Models\Permission;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class SeedPermission
{
    use AsAction;

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        foreach (config('permission.enums') as $enum) {
            foreach ($enum::cases() as $case) {
                $key = Str::snake($case->value);
                Permission::query()
                    ->updateOrCreate(['name' => $key], [
                        'guard_name' => 'web',
                        'label' => "user.permission_name_{$key}",
                    ]);
            }
        }
    }
}
