<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
            'location' => fake()->regexify('[A-Za-z0-9]{255}'),
            'date_start' => fake()->dateTime(),
            'date_end' => fake()->dateTime(),
            'category_id' => Category::factory(),
            'status' => fake()->randomElement(["draft","published","cancelled"]),
        ];
    }
}
