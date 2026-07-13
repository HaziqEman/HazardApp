<?php

namespace Database\Seeders;

use App\Models\Hazard;
use Illuminate\Database\Seeder;

class HazardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hazard::factory()->count(10)->create();
    }
}
