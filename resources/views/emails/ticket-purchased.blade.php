<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        h1 { color: #00F0FF; }
        .ticket { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #00F0FF; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Uspešna kupovina!</h1>
        <p>Poštovani,</p>
        <p>Vaša kupovina karata je uspešno obrađena.</p>

        <h3>📋 Detalji porudžbine:</h3>
        <p><strong>Ukupan iznos:</strong> {{ $order->total_amount }} RSD</p>

        <h3>🎫 Vaše karte:</h3>
        @foreach($tickets as $ticket)
        <div class="ticket">
            <strong>Kod:</strong> {{ $ticket->unique_code }}<br>
            <strong>Cena:</strong> {{ $ticket->price }} RSD
        </div>
        @endforeach

        <p>Hvala što koristite EPA Prodaja Karata!</p>
    </div>
</body>
</html>
