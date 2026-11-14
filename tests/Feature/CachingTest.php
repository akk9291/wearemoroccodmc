<?php

namespace Tests\Feature;

use App\Models;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CachingTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        config(['livewire.inject_assets' => false]);
    }

    public function test_guest_pages_are_cached_and_flushed_when_content_is_edited(): void
    {
        $this->get('/about')->assertOk()->assertHeader('X-Page-Cache', 'MISS');
        $this->get('/about')->assertOk()->assertHeader('X-Page-Cache', 'HIT')->assertSee('Achraf Kassami');

        // A raw query skips model events: the cached page is still served.
        DB::table('pages')->where('slug', 'about')->update(['hero_eyebrow' => 'Raw edit']);
        $this->get('/about')->assertHeader('X-Page-Cache', 'HIT')->assertDontSee('Raw edit');

        // Editing through the model (as the admin does) flushes it.
        Models\Page::where('slug', 'about')->firstOrFail()->update(['hero_eyebrow' => 'Edited in the admin']);
        $this->get('/about')->assertHeader('X-Page-Cache', 'MISS')->assertSee('Edited in the admin');
    }

    public function test_an_edit_anywhere_refreshes_listings_menus_and_settings_on_every_page(): void
    {
        $this->get('/itineraries')->assertSee('The Grand Morocco');
        $this->get('/')->assertOk();

        Models\Itinerary::where('slug', 'grand-tour')->firstOrFail()->update(['title' => 'The Great Morocco']);
        $this->get('/itineraries')->assertHeader('X-Page-Cache', 'MISS')->assertSee('The Great Morocco');

        $item = Models\MenuItem::firstOrFail();
        $item->update(['label' => 'Renamed menu item']);
        $this->get('/')->assertHeader('X-Page-Cache', 'MISS')->assertSee('Renamed menu item');

        Models\Setting::where('key', 'phone_display')->firstOrFail()->update(['value' => '+212 600 000 000']);
        $this->get('/faq')->assertSee('+212 600 000 000');
    }

    public function test_listing_blocks_and_menus_are_cached_between_requests(): void
    {
        config(['cache.page_cache' => false]);
        $this->get('/itineraries')->assertSee('The Grand Morocco');

        DB::table('itineraries')->where('slug', 'grand-tour')->update(['title' => 'Raw title']);
        DB::table('menu_items')->update(['label' => 'Raw label']);
        $this->get('/itineraries')->assertDontSee('Raw title')->assertDontSee('Raw label');

        $queries = 0;
        DB::listen(function () use (&$queries) {
            $queries++;
        });
        $this->get('/itineraries');
        $this->assertLessThanOrEqual(3, $queries, 'cached listings and menus: only the page record is read');
    }

    public function test_each_visitor_gets_their_own_csrf_token_and_forms_work_from_a_cached_page(): void
    {
        $this->app['env'] = 'local'; // CSRF check on, as on the live site

        $first = $this->get('/contact')->assertHeader('X-Page-Cache', 'MISS');
        $this->flushSession();
        $second = $this->get('/contact')->assertHeader('X-Page-Cache', 'HIT');

        $token = session()->token();
        $html = $second->getContent();
        $this->assertStringNotContainsString(\App\Http\Middleware\CacheGuestPages::TOKEN, $html);
        $this->assertStringContainsString('<meta name="csrf-token" content="'.$token.'">', $html);
        $this->assertStringContainsString('name="_token" value="'.$token.'"', $html);
        preg_match('#<meta name="csrf-token" content="([^"]+)">#', $first->getContent(), $m);
        $this->assertNotSame($m[1], $token, 'the cached page does not leak the first visitor\'s token');

        $this->postJson('/forms/newsletter', ['_token' => $token, 'email' => 'cached@example.com'])->assertOk();
        $this->postJson('/forms/newsletter', ['_token' => 'wrong', 'email' => 'other@example.com'])->assertStatus(419);
        $this->app['env'] = 'testing';

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'cached@example.com']);
        $this->assertDatabaseMissing('newsletter_subscribers', ['email' => 'other@example.com']);
    }

    public function test_pages_for_one_visitor_are_never_cached(): void
    {
        // signed-in admin
        $admin = Models\User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin)->get('/about')->assertOk()->assertHeaderMissing('X-Page-Cache');
        $this->actingAs($admin)->get('/about')->assertHeaderMissing('X-Page-Cache');
        auth()->logout();

        // query strings, 404s and form results
        $this->get('/about?utm_source=x')->assertHeaderMissing('X-Page-Cache');
        $this->get('/no-such-page')->assertNotFound()->assertHeaderMissing('X-Page-Cache');
        $this->get('/contact')->assertHeader('X-Page-Cache', 'MISS');
        $this->from('/contact')->post('/forms/contact', ['name' => 'A', 'email' => 'a@example.com', 'message' => 'Hi', 'privacy' => '1'])->assertRedirect();
        // the thank-you notice is shown (not hidden) only to the visitor who sent the form
        $this->get('/contact')->assertHeaderMissing('X-Page-Cache')->assertDontSee('<p class="notice" data-done  hidden', false);
        $this->get('/contact')->assertHeader('X-Page-Cache', 'HIT')->assertSee('<p class="notice" data-done  hidden', false);
    }
}
