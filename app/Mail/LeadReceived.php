<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** To the atelier: a new quote request, inquiry or contact message from the website. */
class LeadReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public const LABELS = ['quote' => 'Quote request', 'inquiry' => 'Inquiry', 'contact' => 'Contact message'];

    /** Column => label, in the order shown in the email. */
    public const FIELDS = ['name' => 'Name', 'email' => 'Email', 'intent' => 'Interest', 'dates' => 'Travel window',
        'travelers' => 'Travelers', 'party' => 'Party size', 'length' => 'Trip length', 'pace' => 'Pace', 'mood' => 'Mood',
        'lead' => 'Lead destination', 'stays' => 'Stays', 'prio' => 'Priorities', 'note' => 'Message', 'brief' => 'Draft brief',
        'source_page' => 'Sent from page'];

    public function __construct(public Model $lead, public string $type) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (self::LABELS[$this->type] ?? 'Website lead').' from '.$this->lead->name,
            replyTo: [new Address($this->lead->email, $this->lead->name)],
        );
    }

    public function content(): Content
    {
        $fields = [];
        foreach (self::FIELDS as $column => $label) {
            if (filled($this->lead->getAttribute($column))) {
                $fields[$label] = $this->lead->getAttribute($column);
            }
        }

        return new Content(view: 'mail.lead-received', text: 'mail.lead-received-text', with: [
            'label' => self::LABELS[$this->type] ?? 'Website lead',
            'fields' => $fields,
            'adminUrl' => url('/admin/'.($this->type === 'contact' ? 'contact-messages' : 'quote-requests').'/'.$this->lead->getKey().'/edit'),
        ]);
    }
}
