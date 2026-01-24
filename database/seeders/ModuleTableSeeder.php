<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Seeder
{
    private array $modules = [

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->modules as $name => $description) {
            $module = Module::query()->firstOrNew(['name' => $name]);
            $module->fill([
                'description' => $description,
                'is_enabled' => true,
                'is_builtin' => true,
            ]);
            $module->save();
        }
    }
}
