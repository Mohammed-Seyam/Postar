<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_subscription_id' => $this->faker->uuid(),
            'plan_id' => \App\Models\Plan::inRandomOrder()->first()?->id ?? \App\Models\Plan::factory(),
            'interval' => $this->faker->randomElement(['month', 'three_months', 'six_months', 'year']),
            'start_at' => now(),
            'expire_at' => now()->addMonth(),
            'status' => 'active',
        ];
    }
}
