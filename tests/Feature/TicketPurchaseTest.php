<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Category;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_dashboard()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'username' => 'testuser',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_user_can_view_event_details()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'username' => 'testuser2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $category = Category::create(['name' => 'Test', 'description' => 'Test']);

        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test',
            'location' => 'Test Location',
            'date_start' => now()->addDays(1),
            'date_end' => now()->addDays(2),
            'category_id' => $category->id,
            'status' => 'published',
            'latitude' => 44.8176,
            'longitude' => 20.4633
        ]);

        $response = $this->actingAs($user)->get("/events/{$event->id}");

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
