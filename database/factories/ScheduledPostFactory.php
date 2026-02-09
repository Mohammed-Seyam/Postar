<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduledPost>
 */
class ScheduledPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'video_id' => Video::factory(),
            'platform' => $this->faker->randomElement(['tiktok', 'instagram', 'youtube']),
            'publish_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'caption' => $this->faker->sentence(),
            'hashtags' => collect($this->faker->words(3))->map(fn ($word) => "#{$word}")->implode(' '),
            'status' => $this->faker->randomElement(['pending', 'published', 'failed']),
        ];
    }
}
