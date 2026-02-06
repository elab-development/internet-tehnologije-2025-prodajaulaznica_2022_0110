<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Category;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Exit Festival 2026',
                'description' => 'Najveći muzički festival u regionu',
                'location' => 'Petrovaradin, Novi Sad',
                'date_start' => now()->addDays(30),
                'date_end' => now()->addDays(34),
                'category_id' => Category::where('name', 'Festival')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'Zvezda vs Partizan',
                'description' => 'Večiti derbi srpskog fudbala',
                'location' => 'Stadion Rajko Mitić, Beograd',
                'date_start' => now()->addDays(15),
                'date_end' => now()->addDays(15)->addHours(2),
                'category_id' => Category::where('name', 'Sport')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'Hamlet - Narodno pozorište',
                'description' => 'Klasična Šekspirova drama',
                'location' => 'Narodno pozorište, Beograd',
                'date_start' => now()->addDays(7),
                'date_end' => now()->addDays(7)->addHours(3),
                'category_id' => Category::where('name', 'Pozorište')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'Tech Summit 2026',
                'description' => 'Konferencija o najnovijim tehnologijama',
                'location' => 'Sava Centar, Beograd',
                'date_start' => now()->addDays(45),
                'date_end' => now()->addDays(47),
                'category_id' => Category::where('name', 'Konferencija')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'Riblja čorba - Koncert',
                'description' => 'Legendarna rok grupa uživo',
                'location' => 'Arena Beograd',
                'date_start' => now()->addDays(20),
                'date_end' => now()->addDays(20)->addHours(4),
                'category_id' => Category::where('name', 'Koncert')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'Beer Fest',
                'description' => 'Festival piva i muzike',
                'location' => 'Ušće, Beograd',
                'date_start' => now()->addDays(60),
                'date_end' => now()->addDays(65),
                'category_id' => Category::where('name', 'Festival')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'NBA Exhibition Game',
                'description' => 'Egzibiciona utakmica NBA zvezda',
                'location' => 'Štark Arena, Beograd',
                'date_start' => now()->addDays(90),
                'date_end' => now()->addDays(90)->addHours(3),
                'category_id' => Category::where('name', 'Sport')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'Biznis forum',
                'description' => 'Forum za preduzetnike i investitore',
                'location' => 'Hotel Hyatt, Beograd',
                'date_start' => now()->addDays(25),
                'date_end' => now()->addDays(26),
                'category_id' => Category::where('name', 'Konferencija')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'David Gilmour - Live',
                'description' => 'Legendarni gitarista Pink Floyd-a',
                'location' => 'Tašmajdan, Beograd',
                'date_start' => now()->addDays(50),
                'date_end' => now()->addDays(50)->addHours(3),
                'category_id' => Category::where('name', 'Koncert')->first()->id,
                'status' => 'published',
            ],
            [
                'title' => 'Karneval',
                'description' => 'Tradicionalni karneval sa maskama',
                'location' => 'Centar grada, Novi Sad',
                'date_start' => now()->addDays(100),
                'date_end' => now()->addDays(102),
                'category_id' => Category::where('name', 'Festival')->first()->id,
                'status' => 'published',
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::create($eventData);

            // Kreiraj ticket types za svaki event
            TicketType::create([
                'event_id' => $event->id,
                'name' => 'standard',
                'price' => rand(1000, 3000),
                'capacity' => rand(500, 1000),
                'sold' => rand(0, 100),
            ]);

            TicketType::create([
                'event_id' => $event->id,
                'name' => 'premium',
                'price' => rand(4000, 7000),
                'capacity' => rand(200, 400),
                'sold' => rand(0, 50),
            ]);

            TicketType::create([
                'event_id' => $event->id,
                'name' => 'vip',
                'price' => rand(8000, 15000),
                'capacity' => rand(50, 150),
                'sold' => rand(0, 20),
            ]);
        }
    }
}
