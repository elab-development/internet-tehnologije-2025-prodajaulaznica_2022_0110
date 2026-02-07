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

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'tickets' => 'required|array',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $ticketsToCreate = [];

            // Proveri dostupnost i izračunaj total
            foreach ($validated['tickets'] as $ticketData) {
                $ticketType = TicketType::lockForUpdate()->find($ticketData['ticket_type_id']);

                $available = $ticketType->capacity - $ticketType->sold;

                if ($available < $ticketData['quantity']) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Nema dovoljno dostupnih karata za {$ticketType->name}.");
                }

                $totalAmount += $ticketType->price * $ticketData['quantity'];

                $ticketsToCreate[] = [
                    'ticket_type' => $ticketType,
                    'quantity' => $ticketData['quantity'],
                ];
            }

            // Kreiraj order
            $order = Order::create([
                'user_id' => Auth::id(),
                'event_id' => $validated['event_id'],
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            // Kreiraj tickets
            foreach ($ticketsToCreate as $ticketData) {
                $ticketType = $ticketData['ticket_type'];

                for ($i = 0; $i < $ticketData['quantity']; $i++) {
                    Ticket::create([
                        'user_id' => Auth::id(),
                        'event_id' => $validated['event_id'],
                        'ticket_type_id' => $ticketType->id,
                        'order_id' => $order->id,
                        'unique_code' => strtoupper(Str::random(10)),
                        'price' => $ticketType->price,
                        'purchased_at' => now(),
                        'status' => 'active',
                    ]);
                }

                // Ažuriraj sold count
                $ticketType->increment('sold', $ticketData['quantity']);
            }

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Karte su uspešno kupljene!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Greška pri kupovini: ' . $e->getMessage());
        }
    }
}
