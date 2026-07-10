<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['id' => 1, 'name' => 'Service (Kebaktian/Ibadah)'],
            ['id' => 2, 'name' => 'Fellowship (Persekutuan)'],
            ['id' => 3, 'name' => 'Gathering (Kebersamaan)'],
            ['id' => 4, 'name' => 'Other (Lainnya)'],
        ];

        foreach ($types as $type) {
            \App\Models\EventType::updateOrCreate(['id' => $type['id']], $type);
        }
    }
}
