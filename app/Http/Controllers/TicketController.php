<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['event', 'ticketType', 'order'])
            ->where('user_id', Auth::id())
            ->orderBy('purchased_at', 'desc')
            ->get();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }
}
