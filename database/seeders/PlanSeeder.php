<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Upsert plans to avoid duplicates
        DB::table('plans')->upsert([
            [
                'name' => 'Monthly Plan',
                'slug' => 'monthly',
                'price' => 9.99,
                'video_limit' => 10,
                'storage_limit' => 1024 * 1024 * 1024, // 1GB
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yearly Plan',
                'slug' => 'yearly',
                'price' => 99.00,
                'video_limit' => 100,
                'storage_limit' => 1024 * 1024 * 1024, // 1GB
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['slug'], ['name', 'price', 'video_limit', 'storage_limit', 'updated_at']);
    }
}
