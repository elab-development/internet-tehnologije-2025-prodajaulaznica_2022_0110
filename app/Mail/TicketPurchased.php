<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $tickets;

    public function __construct($order, $tickets)
    {
        $this->order = $order;
        $this->tickets = $tickets;
    }

    public function build()
    {
        return $this->subject('Uspešna kupovina karata - EPA Prodaja Karata')
                    ->view('emails.ticket-purchased');
    }
}
