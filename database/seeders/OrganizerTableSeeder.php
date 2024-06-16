<?php

namespace Database\Seeders;

use App\Models\Event\Organizer;
use Illuminate\Database\Seeder;

class OrganizerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organizer::factory(100)->create();
    }
}
