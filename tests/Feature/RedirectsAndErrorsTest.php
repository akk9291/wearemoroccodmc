<?php

namespace Tests\Feature;

use App\Filament\Resources\RedirectResource;
use App\Models;
use App\Models\Redirect;
use Database\Seeders\RedirectSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RedirectsAndErrorsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        config(['livewire.inject_assets' => false]);
    }

    public function test_old_html_addresses_still_301_with_their_query_string(): void
    {
        $this->assertSame(count(RedirectSeeder::LEGACY), Redirect::where('source', 'legacy')->count());

        foreach (RedirectSeeder::LEGACY as $file => $target) {
            $this->get('/'.$file)->assertStatus(301)->assertRedirect(route(...$target));
        }
        $this->get('/itinerary-grand-tour.html?utm_source=mail&x=1')->assertStatus(301)
            ->assertRedirect(url('/itineraries/grand-tour').'?utm_source=mail&x=1');
    }

    public function test_middleware_follows_the_table_counts_hits_and_supports_302(): void
    {
        $r = Redirect::create(['from_path' => 'old-offer/', 'to_path' => '/itineraries', 'status_code' => 302]);
        $this->assertSame('/old-offer', $r->from_path, 'paths are normalized');

        $this->get('/old-offer')->assertStatus(302)->assertRedirect(url('/itineraries'));
        $this->get('/old-offer/')->assertStatus(302);
        $this->assertSame(2, $r->fresh()->hits);
        $this->assertNotNull($r->fresh()->last_hit_at);

        Redirect::create(['from_path' => '/partner', 'to_path' => 'https://example.com/landing', 'status_code' => 301]);
        $this->get('/partner')->assertStatus(301)->assertRedirect('https://example.com/landing');

        // edits apply at once (cached map cleared on save) and POSTs are never redirected
        $r->update(['to_path' => '/contact']);
        $this->get('/old-offer')->assertRedirect(url('/contact'));
        $this->post('/old-offer')->assertNotFound();
    }

    public function test_changing_a_slug_adds_a_301_from_the_old_address(): void
    {
        $post = Models\JournalPost::where('slug', 'best-time-to-visit-marrakech')->firstOrFail();
        $post->update(['slug' => 'when-to-visit-marrakech']);

        $this->assertDatabaseHas('redirects', ['from_path' => '/journal/best-time-to-visit-marrakech',
            'to_path' => '/journal/when-to-visit-marrakech', 'status_code' => 301, 'source' => 'slug']);
        $this->get('/journal/best-time-to-visit-marrakech')->assertStatus(301)->assertRedirect(url('/journal/when-to-visit-marrakech'));
        $this->get('/journal/when-to-visit-marrakech')->assertOk();
        // the old .html address now goes straight to the new slug (no chain)
        $this->get('/journal-post.html')->assertRedirect(url('/journal/when-to-visit-marrakech'));

        // renaming back removes the loop and points the other way
        $post->update(['slug' => 'best-time-to-visit-marrakech']);
        $this->get('/journal/best-time-to-visit-marrakech')->assertOk();
        $this->get('/journal/when-to-visit-marrakech')->assertRedirect(url('/journal/best-time-to-visit-marrakech'));
    }

    public function test_slug_change_in_the_admin_form_adds_the_redirect(): void
    {
        $admin = Models\User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $itinerary = Models\Itinerary::where('slug', 'desert-kasbahs')->firstOrFail();
        Livewire::test(\App\Filament\Resources\ItineraryResource\Pages\EditItinerary::class, ['record' => $itinerary->getRouteKey()])
            ->fillForm(['slug' => 'sahara-and-kasbahs'])->call('save')->assertHasNoFormErrors();

        $this->get('/itineraries/desert-kasbahs')->assertStatus(301)->assertRedirect(url('/itineraries/sahara-and-kasbahs'));
        $this->get('/itinerary-desert-kasbahs.html')->assertRedirect(url('/itineraries/sahara-and-kasbahs'));
    }

    public function test_admin_can_manage_redirects(): void
    {
        $admin = Models\User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get(RedirectResource::getUrl('index'))->assertOk()->assertSee('/about.html');
        Livewire::test(RedirectResource\Pages\CreateRedirect::class)
            ->fillForm(['from_path' => '/summer-offer', 'to_path' => '/quote', 'status_code' => 302])
            ->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('redirects', ['from_path' => '/summer-offer', 'to_path' => '/quote', 'status_code' => 302, 'source' => 'manual']);
        $this->get('/summer-offer')->assertStatus(302);

        Livewire::test(RedirectResource\Pages\CreateRedirect::class)
            ->fillForm(['from_path' => '/admin/users', 'to_path' => '/'])->call('create')->assertHasFormErrors(['from_path']);
    }

    public function test_unknown_pages_get_the_designed_404(): void
    {
        foreach (['/no-such-page', '/journal/no-such-post', '/itineraries/nowhere'] as $uri) {
            $this->get($uri)->assertNotFound()
                ->assertSee('A wrong turn', false)
                ->assertSee('<meta name="robots" content="noindex, follow">', false)
                ->assertSee('class="site-footer"', false)
                ->assertSee(route('itineraries.index'), false)
                ->assertSee(route('contact'), false);
        }
    }

    public function test_server_error_page_renders_without_the_database(): void
    {
        $queries = 0;
        \DB::listen(function () use (&$queries) {
            $queries++;
        });
        $html = view('errors.500')->render();

        $this->assertSame(0, $queries, 'the 500 page must not touch the database');
        $this->assertStringContainsString('Something went wrong', $html);
        $this->assertStringContainsString('noindex', $html);
        $this->assertStringContainsString(asset('assets/arena.css'), $html);
    }
}
