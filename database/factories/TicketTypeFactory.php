<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->randomElement(["standard","premium","vip"]),
            'price' => fake()->randomFloat(2, 0, 99999999.99),
            'capacity' => fake()->randomNumber(),
            'sold' => fake()->randomNumber(),
        ];
    }
}
