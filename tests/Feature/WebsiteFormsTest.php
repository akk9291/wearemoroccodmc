<?php

namespace Tests\Feature;

use App\Mail\GuestConfirmation;
use App\Mail\LeadReceived;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WebsiteFormsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private const JSON = ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'];

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['livewire.inject_assets' => false]);
    }

    private function inquiry(array $overrides = []): array
    {
        return $overrides + ['name' => 'Jane Traveler', 'email' => 'jane@example.com', 'dates' => 'Apr 2027', 'travelers' => '2 adults',
            'intent' => 'imperial-morocco', 'note' => 'Slow mornings please.', 'source_page' => '/about'];
    }

    private function quote(array $overrides = []): array
    {
        return $overrides + $this->inquiry(['intent' => 'custom', 'source_page' => '/quote', 'pace' => 'Balanced', 'mood' => 'Private',
            'party' => 'Just two', 'length' => '7 — 9 nights', 'lead' => 'Marrakech', 'stays' => 'Riads', 'prio' => 'The desert, Food',
            'brief' => "Pace: Balanced\nMood: Private"]);
    }

    private function contact(array $overrides = []): array
    {
        return $overrides + ['name' => 'Sam Planner', 'email' => 'sam@example.com', 'message' => 'Do you handle groups of 40?',
            'privacy' => '1', 'source_page' => '/contact'];
    }

    // ---- inquiry drawer ----

    public function test_inquiry_is_saved_and_both_emails_are_queued(): void
    {
        Setting::updateOrCreate(['key' => 'notification_email'], ['value' => 'leads@example.com', 'group' => 'notifications']);

        $this->postJson('/forms/inquiry', $this->inquiry(['brief' => 'Draft from the builder']), self::JSON)
            ->assertOk()->assertJson(['ok' => true, 'redirect' => null]);

        $lead = QuoteRequest::sole();
        $this->assertSame('inquiry', $lead->form_type);
        $this->assertSame('/about', $lead->source_page);
        $this->assertSame('Jane Traveler', $lead->name);
        $this->assertSame('Draft from the builder', $lead->brief);
        $this->assertSame('new', $lead->status);
        Mail::assertQueued(LeadReceived::class, fn ($m) => $m->hasTo('leads@example.com') && $m->hasReplyTo('jane@example.com') && $m->type === 'inquiry');
        Mail::assertQueued(GuestConfirmation::class, fn ($m) => $m->hasTo('jane@example.com'));
    }

    public function test_inquiry_validates(): void
    {
        $this->postJson('/forms/inquiry', $this->inquiry(['name' => '', 'email' => 'not-an-email']), self::JSON)
            ->assertUnprocessable()->assertJsonValidationErrors(['name', 'email']);

        $this->assertSame(0, QuoteRequest::count());
        Mail::assertNothingQueued();
    }

    public function test_inquiry_without_javascript_redirects_to_thank_you(): void
    {
        $this->post('/forms/inquiry', $this->inquiry())->assertRedirect(route('thank-you'));
        $this->assertSame(1, QuoteRequest::count());
    }

    // ---- quote builder ----

    public function test_quote_is_saved_with_builder_answers_and_goes_to_thank_you(): void
    {
        $this->postJson('/forms/quote', $this->quote(), self::JSON)
            ->assertOk()->assertJson(['ok' => true, 'redirect' => route('thank-you')]);

        $lead = QuoteRequest::sole();
        $this->assertSame('quote', $lead->form_type);
        $this->assertSame('/quote', $lead->source_page);
        $this->assertSame('Balanced', $lead->pace);
        $this->assertSame('The desert, Food', $lead->prio);
        $this->assertStringContainsString('Mood: Private', $lead->brief);
        Mail::assertQueued(LeadReceived::class, fn ($m) => $m->type === 'quote');
        Mail::assertQueued(GuestConfirmation::class);
    }

    public function test_quote_validates(): void
    {
        $this->postJson('/forms/quote', $this->quote(['email' => '', 'pace' => str_repeat('x', 600)]), self::JSON)
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'pace']);
        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_notification_falls_back_when_setting_is_empty(): void
    {
        Setting::where('key', 'notification_email')->delete();
        \Illuminate\Support\Facades\Cache::forget('settings');

        $this->postJson('/forms/quote', $this->quote(), self::JSON)->assertOk();
        Mail::assertQueued(LeadReceived::class, fn ($m) => $m->hasTo('info@wearemoroccotravel.com'));
    }

    // ---- contact ----

    public function test_contact_is_saved_and_emails_are_queued(): void
    {
        $this->postJson('/forms/contact', $this->contact(), self::JSON)->assertOk()->assertJson(['ok' => true]);

        $message = ContactMessage::sole();
        $this->assertSame('contact', $message->form_type);
        $this->assertSame('/contact', $message->source_page);
        $this->assertSame('Do you handle groups of 40?', $message->note);
        Mail::assertQueued(LeadReceived::class, fn ($m) => $m->type === 'contact' && $m->hasReplyTo('sam@example.com'));
        Mail::assertQueued(GuestConfirmation::class, fn ($m) => $m->hasTo('sam@example.com'));
    }

    public function test_contact_validates_including_privacy(): void
    {
        $this->postJson('/forms/contact', $this->contact(['message' => '', 'privacy' => null]), self::JSON)
            ->assertUnprocessable()->assertJsonValidationErrors(['message', 'privacy']);
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_without_javascript_shows_errors_then_thanks(): void
    {
        $this->from('/contact')->post('/forms/contact', $this->contact(['name' => '']))
            ->assertRedirect('/contact')->assertSessionHasErrors('name', null, 'contact');
        $this->get('/contact')->assertSee('Please add your name.');

        $this->from('/contact')->post('/forms/contact', $this->contact())->assertRedirect('/contact#contact-form');
        $this->get('/contact')->assertSee('Your message is with the atelier', false)->assertDontSee('<p class="notice" data-done  hidden', false);
        $this->assertSame(1, ContactMessage::count());
    }

    // ---- newsletter ----

    public function test_newsletter_subscribes_and_duplicate_is_a_silent_success(): void
    {
        $this->postJson('/forms/newsletter', ['email' => 'fan@example.com', 'source_page' => '/journal'], self::JSON)
            ->assertOk()->assertJson(['ok' => true]);
        $this->postJson('/forms/newsletter', ['email' => ' FAN@example.com ', 'source_page' => '/'], self::JSON)
            ->assertOk()->assertJson(['ok' => true]);

        $this->assertSame(1, NewsletterSubscriber::count());
        $this->assertSame('/journal', NewsletterSubscriber::sole()->source);
        Mail::assertNothingQueued();
    }

    public function test_newsletter_validates(): void
    {
        $this->postJson('/forms/newsletter', ['email' => 'nope'], self::JSON)
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertSame(0, NewsletterSubscriber::count());
    }

    // ---- shared protections ----

    public function test_honeypot_answers_success_but_saves_and_sends_nothing(): void
    {
        $bot = ['website' => 'http://spam.example'];
        $this->postJson('/forms/inquiry', $this->inquiry($bot), self::JSON)->assertOk()->assertJson(['ok' => true]);
        $this->postJson('/forms/quote', $this->quote($bot), self::JSON)->assertOk();
        $this->postJson('/forms/contact', $this->contact($bot), self::JSON)->assertOk();
        $this->postJson('/forms/newsletter', ['email' => 'bot@example.com'] + $bot, self::JSON)->assertOk();

        $this->assertSame(0, QuoteRequest::count());
        $this->assertSame(0, ContactMessage::count());
        $this->assertSame(0, NewsletterSubscriber::count());
        Mail::assertNothingQueued();
    }

    public function test_forms_are_rate_limited(): void
    {
        foreach (['/forms/inquiry' => $this->inquiry(), '/forms/quote' => $this->quote(), '/forms/contact' => $this->contact(),
            '/forms/newsletter' => ['email' => 'a@example.com']] as $uri => $payload) {
            for ($i = 0; $i < 5; $i++) {
                $this->postJson($uri, $payload, self::JSON)->assertOk();
            }
            $this->postJson($uri, $payload, self::JSON)->assertStatus(429);
        }
        $this->assertSame(5, ContactMessage::count());
    }

    public function test_forms_require_a_csrf_token(): void
    {
        // The testing kernel skips CSRF; turn it back on for this request.
        $this->app['env'] = 'local';
        $response = $this->postJson('/forms/contact', $this->contact(), self::JSON);
        $this->app['env'] = 'testing';

        $response->assertStatus(419);
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_every_page_carries_the_form_fields(): void
    {
        $html = $this->get('/about')->assertOk()->getContent();
        $this->assertStringContainsString('name="csrf-token"', $html);
        $this->assertStringContainsString('action="'.route('forms.inquiry').'"', $html);
        $this->assertStringContainsString('data-quote-action="'.route('forms.quote').'"', $html);
        $this->assertStringContainsString('action="'.route('forms.newsletter').'"', $html);
        $this->assertStringContainsString('name="website"', $html);
        $this->assertStringContainsString('name="source_page" value="/about"', $html);
        $this->assertStringContainsString('assets/forms.js', $html);
        $this->assertLessThan(strpos($html, 'assets/forms.js'), strpos($html, 'assets/arena.js'));

        $this->get('/contact')->assertSee('action="'.route('forms.contact').'"', false)->assertSee('name="message"', false);
        $this->get('/journal')->assertSee('id="j-news"', false)->assertSee('Subscribe');
    }
}
