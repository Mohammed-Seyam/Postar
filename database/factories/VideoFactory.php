<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'file_path' => $this->faker->filePath(),
            'thumbnail_path' => $this->faker->imageUrl(),
            'duration' => $this->faker->numberBetween(10, 300),
            'status' => 'draft',
        ];
    }
}
