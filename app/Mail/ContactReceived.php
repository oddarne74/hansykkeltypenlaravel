<?php
namespace App\Mail;
use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class ContactReceived extends Mailable implements ShouldQueue
{ use Queueable,SerializesModels; public function __construct(public ContactRequest $request){} public function envelope():Envelope{return new Envelope(subject:'Ny henvendelse – Han Sykkeltypen');} public function content():Content{return new Content(view:'mail.contact-received');} }
