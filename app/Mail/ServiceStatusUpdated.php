<?php

namespace App\Mail;

use App\Enums\ServiceStatus;
use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceRequest $serviceRequest) {}

    public function envelope(): Envelope
    {
        $subject = $this->serviceRequest->status === ServiceStatus::APPROVED
            ? 'Serviceforespørselen din er godkjent – Han Sykkeltypen'
            : 'Serviceforespørselen din – Han Sykkeltypen';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.service-status');
    }
}
