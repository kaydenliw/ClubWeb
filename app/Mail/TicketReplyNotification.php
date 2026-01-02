<?php

namespace App\Mail;

use App\Models\ContactTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactTicket $ticket
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reply to Your Ticket: ' . $this->ticket->ticket_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.ticket-reply',
        );
    }
}
