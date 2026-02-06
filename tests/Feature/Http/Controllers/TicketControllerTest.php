<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TicketController
 */
final class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_displays_view(): void
    {
        $tickets = Ticket::factory()->count(3)->create();

        $response = $this->get(route('tickets.index'));

        $response->assertOk();
        $response->assertViewIs('ticket.index');
        $response->assertViewHas('tickets', $tickets);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->get(route('tickets.show', $ticket));

        $response->assertOk();
        $response->assertViewIs('ticket.show');
        $response->assertViewHas('ticket', $ticket);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->delete(route('tickets.destroy', $ticket));

        $response->assertRedirect(route('ticket.index'));

        $this->assertModelMissing($ticket);
    }
}
