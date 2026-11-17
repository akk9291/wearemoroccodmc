<?php

namespace App\Http\Controllers;

use App\Http\Requests\Forms\ContactRequest;
use App\Http\Requests\Forms\InquiryRequest;
use App\Http\Requests\Forms\NewsletterRequest;
use App\Http\Requests\Forms\QuoteRequestForm;
use App\Http\Requests\Forms\WebsiteFormRequest;
use App\Mail\GuestConfirmation;
use App\Mail\LeadReceived;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

/**
 * Public website forms. Each answers JSON to public/assets/forms.js, or redirects when posted without JavaScript.
 * A filled honeypot gets the normal success answer, but nothing is saved or sent.
 */
class FormController extends Controller
{
    public const FALLBACK_NOTIFICATION_EMAIL = 'info@wearemoroccodmc.com';

    public function inquiry(InquiryRequest $request): JsonResponse|RedirectResponse
    {
        return $this->lead($request, 'inquiry');
    }

    public function quote(QuoteRequestForm $request): JsonResponse|RedirectResponse
    {
        return $this->lead($request, 'quote');
    }

    public function contact(ContactRequest $request): JsonResponse|RedirectResponse
    {
        if (! $request->isSpam()) {
            $values = $request->values();
            $message = ContactMessage::create([
                'name' => $values['name'], 'email' => $values['email'], 'note' => $values['message'],
                'form_type' => 'contact', 'source_page' => $request->sourcePage(), 'ip' => $request->ip(),
            ]);
            $this->notify($message, 'contact');
        }

        return $this->done($request, back()->withFragment('contact-form')->with('form_sent', 'contact'));
    }

    public function newsletter(NewsletterRequest $request): JsonResponse|RedirectResponse
    {
        if (! $request->isSpam()) {
            // A second sign-up with the same address is answered like the first one.
            NewsletterSubscriber::firstOrCreate(
                ['email' => mb_strtolower(trim($request->values()['email']))],
                ['source' => $request->sourcePage()],
            );
        }

        return $this->done($request, back()->with('form_sent', 'newsletter'));
    }

    private function lead(WebsiteFormRequest $request, string $type): JsonResponse|RedirectResponse
    {
        if (! $request->isSpam()) {
            $lead = QuoteRequest::create($request->values() + [
                'form_type' => $type, 'source_page' => $request->sourcePage(), 'ip' => $request->ip(),
            ]);
            $this->notify($lead, $type);
        }

        return $this->done($request, redirect()->route('thank-you'), $type === 'quote' ? route('thank-you') : null);
    }

    /** Both queued: notification to the atelier (reply-to = guest) and a short confirmation to the guest. */
    private function notify(Model $lead, string $type): void
    {
        Mail::to(static::notificationEmail())->send(new LeadReceived($lead, $type));
        Mail::to($lead->email, $lead->name)->send(new GuestConfirmation($lead->name, $type));
    }

    /** "Lead notification email" from Site settings, else the fallback address. */
    public static function notificationEmail(): string
    {
        $email = Setting::get('notification_email');

        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : static::FALLBACK_NOTIFICATION_EMAIL;
    }

    private function done(WebsiteFormRequest $request, RedirectResponse $redirect, ?string $next = null): JsonResponse|RedirectResponse
    {
        return $request->expectsJson() ? response()->json(['ok' => true, 'redirect' => $next]) : $redirect;
    }
}
