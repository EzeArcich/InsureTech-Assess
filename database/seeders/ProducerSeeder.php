<?php

namespace Database\Seeders;

use App\Models\Producer;
use Illuminate\Database\Seeder;

class ProducerSeeder extends Seeder
{
    public function run(): void
    {
        $missing = max(0, 8 - Producer::count());
        if ($missing > 0) {
            Producer::factory()->count($missing)->create();
        }
    }
}
