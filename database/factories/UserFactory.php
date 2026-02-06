<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'surname' => fake()->regexify('[A-Za-z0-9]{100}'),
            'username' => fake()->userName(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => fake()->dateTime(),
            'password' => fake()->password(),
            'role' => fake()->randomElement(["user","moderator","admin"]),
            'remember_token' => fake()->uuid(),
        ];
    }
}
