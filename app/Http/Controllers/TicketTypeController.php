<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketTypeStoreRequest;
use App\Http\Requests\TicketTypeUpdateRequest;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $ticketTypes = TicketType::all();

        return view('ticket_type.index', [
            'ticket_types' => $ticket_types,
        ]);
    }

    public function store(TicketTypeStoreRequest $request): Response
    {
        $ticketType = TicketType::create($request->validated());

        return redirect()->route('event.show', ['event' => $event]);
    }

    public function update(TicketTypeUpdateRequest $request, TicketType $ticketType): Response
    {
        $ticketType->update($request->validated());

        return redirect()->route('event.show', ['event' => $event]);
    }

    public function destroy(Request $request, TicketType $ticketType): Response
    {
        $ticketType->delete();

        return redirect()->route('event.show', ['event' => $event]);
    }
}
