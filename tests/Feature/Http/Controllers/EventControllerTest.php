<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\EventController
 */
final class EventControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $events = Event::factory()->count(3)->create();

        $response = $this->get(route('events.index'));

        $response->assertOk();
        $response->assertViewIs('event.index');
        $response->assertViewHas('events', $events);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $event = Event::factory()->create();

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $response->assertViewIs('event.show');
        $response->assertViewHas('event', $event);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\EventController::class,
            'store',
            \App\Http\Requests\EventStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $title = fake()->sentence(4);
        $description = fake()->text();
        $location = fake()->word();
        $date_start = Carbon::parse(fake()->dateTime());
        $date_end = Carbon::parse(fake()->dateTime());
        $category = Category::factory()->create();
        $status = fake()->randomElement(/** enum_attributes **/);

        $response = $this->post(route('events.store'), [
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'category_id' => $category->id,
            'status' => $status,
        ]);

        $events = Event::query()
            ->where('title', $title)
            ->where('description', $description)
            ->where('location', $location)
            ->where('date_start', $date_start)
            ->where('date_end', $date_end)
            ->where('category_id', $category->id)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $events);
        $event = $events->first();

        $response->assertRedirect(route('event.index'));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\EventController::class,
            'update',
            \App\Http\Requests\EventUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $event = Event::factory()->create();
        $title = fake()->sentence(4);
        $description = fake()->text();
        $location = fake()->word();
        $date_start = Carbon::parse(fake()->dateTime());
        $date_end = Carbon::parse(fake()->dateTime());
        $category = Category::factory()->create();
        $status = fake()->randomElement(/** enum_attributes **/);

        $response = $this->put(route('events.update', $event), [
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'category_id' => $category->id,
            'status' => $status,
        ]);

        $event->refresh();

        $response->assertRedirect(route('event.show', ['event' => $event]));

        $this->assertEquals($title, $event->title);
        $this->assertEquals($description, $event->description);
        $this->assertEquals($location, $event->location);
        $this->assertEquals($date_start, $event->date_start);
        $this->assertEquals($date_end, $event->date_end);
        $this->assertEquals($category->id, $event->category_id);
        $this->assertEquals($status, $event->status);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $event = Event::factory()->create();

        $response = $this->delete(route('events.destroy', $event));

        $response->assertRedirect(route('event.index'));

        $this->assertModelMissing($event);
    }
}
