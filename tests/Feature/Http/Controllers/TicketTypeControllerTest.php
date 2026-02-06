<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TicketTypeController
 */
final class TicketTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $ticketTypes = TicketType::factory()->count(3)->create();

        $response = $this->get(route('ticket-types.index'));

        $response->assertOk();
        $response->assertViewIs('ticket_type.index');
        $response->assertViewHas('ticket_types');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TicketTypeController::class,
            'store',
            \App\Http\Requests\TicketTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $event = Event::factory()->create();
        $name = fake()->name();
        $price = fake()->randomFloat(/** decimal_attributes **/);
        $capacity = fake()->numberBetween(-10000, 10000);

        $response = $this->post(route('ticket-types.store'), [
            'event_id' => $event->id,
            'name' => $name,
            'price' => $price,
            'capacity' => $capacity,
        ]);

        $ticketTypes = TicketType::query()
            ->where('event_id', $event->id)
            ->where('name', $name)
            ->where('price', $price)
            ->where('capacity', $capacity)
            ->get();
        $this->assertCount(1, $ticketTypes);
        $ticketType = $ticketTypes->first();

        $response->assertRedirect(route('event.show', ['event' => $event]));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TicketTypeController::class,
            'update',
            \App\Http\Requests\TicketTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $ticketType = TicketType::factory()->create();
        $name = fake()->name();
        $price = fake()->randomFloat(/** decimal_attributes **/);
        $capacity = fake()->numberBetween(-10000, 10000);

        $response = $this->put(route('ticket-types.update', $ticketType), [
            'name' => $name,
            'price' => $price,
            'capacity' => $capacity,
        ]);

        $ticketType->refresh();

        $response->assertRedirect(route('event.show', ['event' => $event]));

        $this->assertEquals($name, $ticketType->name);
        $this->assertEquals($price, $ticketType->price);
        $this->assertEquals($capacity, $ticketType->capacity);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $ticketType = TicketType::factory()->create();

        $response = $this->delete(route('ticket-types.destroy', $ticketType));

        $response->assertRedirect(route('event.show', ['event' => $event]));

        $this->assertModelMissing($ticketType);
    }
}
