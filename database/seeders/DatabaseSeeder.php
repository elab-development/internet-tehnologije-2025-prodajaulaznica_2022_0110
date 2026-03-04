<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            EventSeeder::class,
        ]);

        $user = \App\Models\User::create([
            'name' => 'Admin',
            'surname' => 'Test',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // TEST TICKETS
        \App\Models\Order::create([
            'user_id' => $user->id,
            'event_id' => 1,
            'total_amount' => 5000,
            'status' => 'completed',
            'created_at' => now()->subDays(1), // ← juče
        ]);

        \App\Models\Order::create([
            'user_id' => $user->id,
            'event_id' => 2,
            'total_amount' => 3000,
            'status' => 'completed',
            'created_at' => now(), // ← danas
        ]);

        for ($i = 0; $i < 5; $i++) {
            \App\Models\Ticket::create([
                'user_id' => $user->id,
                'event_id' => 1,
                'ticket_type_id' => 1,
                'order_id' => 1,
                'unique_code' => strtoupper(\Illuminate\Support\Str::random(10)),
                'price' => 1000,
                'purchased_at' => now()->subDays(1), // ← juče
                'status' => 'active',
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            \App\Models\Ticket::create([
                'user_id' => $user->id,
                'event_id' => 2,
                'ticket_type_id' => 4,
                'order_id' => 2,
                'unique_code' => strtoupper(\Illuminate\Support\Str::random(10)),
                'price' => 1000,
                'purchased_at' => now(), // ← danas
                'status' => 'active',
            ]);
        }
    }
}
