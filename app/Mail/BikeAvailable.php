<?php

namespace App\Mail;

use App\Models\Bike;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BikeAvailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Bike $bike)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sykkelen ' . $this->bike->name . ' er tilgjengelig igjen!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bike-available',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
