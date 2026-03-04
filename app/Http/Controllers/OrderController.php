<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\ProcessTicketPurchase;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        Log::info('OrderController store called', ['user' => Auth::id()]);
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'tickets' => 'required|array',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1',
        ]);
        Log::info('Validation passed', $validated);

        try {
            Log::info('About to dispatch job');
            // Dispatch job to queue (FIFO)
            ProcessTicketPurchase::dispatch(
                Auth::id(),
                $validated['event_id'],
                $validated['tickets']
            );

            Log::info('Job dispatched successfully');

            return redirect()->route('dashboard')->with('success', 'Vaša kupovina je stavljena u red čekanja! Karte će biti dodeljene uskoro.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Greška pri kupovini: ' . $e->getMessage());
        }
    }
}
