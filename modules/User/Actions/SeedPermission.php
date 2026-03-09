<?php

namespace App\Actions\User;

namespace Modules\User\Actions;

use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Models\Permission;
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
                    ->updateOrCreate(['name' => $case->value], [
                        'guard_name' => 'web',
                        'label' => "user.permission.name_{$key}",
                    ]);
            }
        }
    }
}
