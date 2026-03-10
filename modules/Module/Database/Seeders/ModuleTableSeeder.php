<?php

namespace Modules\Module\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Module\Models\Module;
use Modules\ModuleManager;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ModuleManager::getModules() as $module) {
            Module::query()->updateOrCreate(['name' => $module['name']], [
                'description' => $module['description'],
                'is_enabled' => $module['enabled'] ?? true,
                'is_builtin' => $module['builtin'] ?? true,
            ]);
        }
    }
}
