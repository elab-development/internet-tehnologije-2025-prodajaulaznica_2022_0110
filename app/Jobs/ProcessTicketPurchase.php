<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProcessTicketPurchase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $eventId;
    public $tickets;
    public $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $eventId, $tickets)
    {
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->tickets = $tickets;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        sleep(10);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $ticketsToCreate = [];

            // Proveri dostupnost i izračunaj total
            foreach ($this->tickets as $ticketData) {
                $ticketType = TicketType::lockForUpdate()->find($ticketData['ticket_type_id']);

                $available = $ticketType->capacity - $ticketType->sold;

                if ($available < $ticketData['quantity']) {
                    DB::rollBack();
                    Log::error("Nedovoljno karata za ticket_type_id: {$ticketType->id}");
                    throw new \Exception("Nema dovoljno dostupnih karata za {$ticketType->name}.");
                }

                $totalAmount += $ticketType->price * $ticketData['quantity'];

                $ticketsToCreate[] = [
                    'ticket_type' => $ticketType,
                    'quantity' => $ticketData['quantity'],
                ];
            }

            // Kreiraj order
            $order = Order::create([
                'user_id' => $this->userId,
                'event_id' => $this->eventId,
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            $this->orderId = $order->id;

            // Kreiraj tickets
            foreach ($ticketsToCreate as $ticketData) {
                $ticketType = $ticketData['ticket_type'];

                for ($i = 0; $i < $ticketData['quantity']; $i++) {
                    Ticket::create([
                        'user_id' => $this->userId,
                        'event_id' => $this->eventId,
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

            Log::info("Uspešna kupovina - Order ID: {$order->id}, User ID: {$this->userId}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Greška pri kupovini: " . $e->getMessage());
            throw $e;
        }
    }
}
