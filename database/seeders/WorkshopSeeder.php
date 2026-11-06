<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $missing = max(0, 40 - Workshop::count());
        if ($missing > 0) {
            Workshop::factory()->count($missing)->create();
        }
    }
}
