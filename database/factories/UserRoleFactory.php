<?php

namespace Database\Factories;

use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserRole>
 */
class UserRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_at' => fake()->optional()->date(),
            'end_at' => fake()->optional()->date(),
            'is_active' => fake()->boolean(),
            'assign_by' => null, // or fake user id
            'update_by' => null,
            'disabled_at' => fake()->optional()->date(),
            'user_id' => \App\Models\User::factory(),
            'role_id' => \App\Models\Role::factory(),
        ];
    }
}
