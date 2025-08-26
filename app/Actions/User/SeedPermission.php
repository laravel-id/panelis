<?php

namespace App\Actions\User;

use App\Models\Permission;
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
        foreach (config('permission.enums') as $enum) {
            foreach ($enum::cases() as $case) {
                $key = Str::snake($case->value);
                Permission::query()
                    ->updateOrCreate(['name' => $case->value], [
                        'guard_name' => 'web',
                        'label' => "user.permission_name_{$key}",
                    ]);
            }
        }
    }
}
