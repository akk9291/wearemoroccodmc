<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** To the guest: a short note that their message reached the atelier. */
class GuestConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ?string $name, public string $type) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your note is with our atelier in Marrakech');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.guest-confirmation', text: 'mail.guest-confirmation-text', with: [
            'site' => Setting::allValues(),
        ]);
    }
}
