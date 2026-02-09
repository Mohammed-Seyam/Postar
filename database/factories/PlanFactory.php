<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug(),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'video_limit' => $this->faker->numberBetween(10, 100),
            'storage_limit' => $this->faker->numberBetween(1024, 1024 * 1024 * 1024),
        ];
    }
}
