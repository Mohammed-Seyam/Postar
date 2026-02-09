<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
        ]);

        // User::factory(10)->create();

        \App\Models\User::factory(10)
            ->create([
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ])
            ->each(function ($user) {
                // Create Subscription
                \App\Models\Subscription::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Create Videos and ScheduledPosts
                \App\Models\Video::factory(5)
                    ->create([
                        'user_id' => $user->id,
                    ])
                    ->each(function ($video) {
                        \App\Models\ScheduledPost::factory()->create([
                            'video_id' => $video->id,
                        ]);
                    });
            });
    }
}
