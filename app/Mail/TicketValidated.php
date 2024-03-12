<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\BuyTickets;
use App\Models\Events;

class TicketValidated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $qrCode;
    public $event; // Define the event variable


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(BuyTickets $ticket, $qrCode, Events $event)
    {
        $this->ticket = $ticket;
        $this->qrCode = $qrCode; // Assign the qrCode variable
        $this->event = $event; // Assign the event variable

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ticket_validated')
            ->subject('Ticket Validated');
    }
}
