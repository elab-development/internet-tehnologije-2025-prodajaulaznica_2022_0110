<?php

// Simulacija 100 zahteva u 1 sekundi

$eventId = 1; // Promeni na ID postojećeg događaja
$ticketTypeId = 1; // Promeni na ID postojećeg ticket type-a

for ($i = 1; $i <= 100; $i++) {
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "http://localhost:8000/orders",
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode([
            'event_id' => $eventId,
            'tickets' => [
                [
                    'ticket_type_id' => $ticketTypeId,
                    'quantity' => 1
                ]
            ]
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Cookie: laravel_session=eyJpdiI6Im4yd050czV4NXpkcndFVlZ4SGkrZ1E9PSIsInZhbHVlIjoiMUxnOTRJY3Fadk1PMDJ6WGNibUc3YnZaMTZVbml3TGJyQTlVWjNKTTdUMDA3V3RRN3hLUWU4QloxcVVVNzhIdWhlSGJIN0NxSDl5U3lLK2RrU29xcmRWUnlHR0NLQUFQZUh3emRSeFIxVysweTNKV0VRUmt2VDhRUkJsOE9XcFYiLCJtYWMiOiIxZjg1OTk4ZDUwNjFiYTQ5ZTlhMTk3MjM3ZmE5ODQxNWFjODI3YTQ3MzFkMjg4YTIyNDBiYjMyODMxOTRlNGM4IiwidGFnIjoiIn0='
        ],
    ]);

    curl_exec($ch);
    curl_close($ch);

    echo "Zahtev #{$i} poslat\n";

    // Pošalji sve odjednom (bez sleep-a)
}

echo "Poslato 100 zahteva!\n";
