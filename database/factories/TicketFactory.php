<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'ticket_type_id' => TicketType::factory(),
            'order_id' => Order::factory(),
            'unique_code' => fake()->regexify('[A-Za-z0-9]{64}'),
            'price' => fake()->randomFloat(2, 0, 99999999.99),
            'purchased_at' => fake()->dateTime(),
            'status' => fake()->randomElement(["active","used","cancelled"]),
        ];
    }
}
