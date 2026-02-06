<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $tickets = Ticket::all();

        return view('ticket.index', [
            'tickets' => $tickets,
        ]);
    }

    public function show(Request $request, Ticket $ticket): Response
    {
        return view('ticket.show', [
            'ticket' => $ticket,
        ]);
    }

    public function destroy(Request $request, Ticket $ticket): Response
    {
        $ticket->delete();

        return redirect()->route('ticket.index');
    }
}
