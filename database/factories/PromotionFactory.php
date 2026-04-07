<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'pourcentage' => fake()->randomFloat(2, 0, 50),
            'discount' => fake()->randomFloat(2, 0, 100),
            'max_discount' => fake()->randomFloat(2, 0, 200),
            'start_at' => fake()->dateTimeBetween('now', '+1 month'),
            'end_at' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'is_active' => fake()->boolean(),
        ];
    }
}
