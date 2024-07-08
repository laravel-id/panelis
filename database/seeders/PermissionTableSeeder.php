<?php

namespace Database\Seeders;

use App\Actions\User\SeedPermission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeedPermission::run();
    }
}
