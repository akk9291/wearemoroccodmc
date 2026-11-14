<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources;
use App\Models;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** Every public route (29). */
    public const ROUTES = ['/', '/about', '/why-us', '/services', '/services/tailor-made', '/services/incentives', '/services/events',
        '/services/ground-handling', '/destinations', '/destinations/marrakech', '/itineraries', '/itineraries/imperial-cities',
        '/itineraries/magical-morocco', '/itineraries/grand-tour', '/itineraries/desert-kasbahs', '/itineraries/marrakech-incentive',
        '/experiences', '/case-studies', '/case-studies/agafay-gala', '/journal', '/journal/best-time-to-visit-marrakech', '/faq',
        '/quote', '/contact', '/thank-you', '/sustainability', '/terms', '/privacy', '/cookies'];

    public const CONTENT = [Resources\PageResource::class, Resources\ServiceResource::class, Resources\DestinationResource::class,
        Resources\ItineraryResource::class, Resources\ExperienceResource::class, Resources\CaseStudyResource::class,
        Resources\JournalPostResource::class, Resources\JournalCategoryResource::class, Resources\FaqResource::class,
        Resources\TestimonialResource::class, Resources\PartnerResource::class];

    public const LEADS = [Resources\QuoteRequestResource::class, Resources\ContactMessageResource::class,
        Resources\NewsletterSubscriberResource::class];

    protected function setUp(): void
    {
        parent::setUp();
        // Livewire may inject its assets into any page rendered in the same test process; the live site never gets them.
        config(['livewire.inject_assets' => false]);
    }

    protected function user(string $role): Models\User
    {
        $user = Models\User::factory()->create(['email' => strtolower($role).'-'.uniqid().'@test.local']);
        $user->assignRole($role);

        return $user;
    }

    protected function snapshot(): array
    {
        $out = [];
        foreach (self::ROUTES as $route) {
            $res = $this->get($route);
            $res->assertOk();
            $out[$route] = $res->getContent();
        }

        return $out;
    }

    public function test_role_access_matrix(): void
    {
        $expect = [
            'Admin' => ['content' => 200, 'leads' => 200, 'menus' => 200, 'settings' => 200, 'system' => 200],
            'Editor' => ['content' => 200, 'leads' => 403, 'menus' => 200, 'settings' => 403, 'system' => 403],
            'Sales' => ['content' => 403, 'leads' => 200, 'menus' => 403, 'settings' => 403, 'system' => 403],
        ];
        foreach ($expect as $role => $codes) {
            $this->actingAs($this->user($role));
            foreach (self::CONTENT as $r) {
                $this->get($r::getUrl('index'))->assertStatus($codes['content']);
            }
            foreach (self::LEADS as $r) {
                $this->get($r::getUrl('index'))->assertStatus($codes['leads']);
            }
            $this->get(Resources\MenuResource::getUrl('index'))->assertStatus($codes['menus']);
            $this->get(SiteSettings::getUrl())->assertStatus($codes['settings']);
            $this->get(Resources\UserResource::getUrl('index'))->assertStatus($codes['system']);
            $this->get(Resources\RoleResource::getUrl('index'))->assertStatus($codes['system']);
            $this->get('/admin')->assertOk();
        }

        // no role -> no panel
        $this->actingAs(Models\User::factory()->create())->get('/admin')->assertForbidden();
    }

    /** Opening and saving every record without changes must not change the public site. */
    public function test_saving_every_record_unchanged_keeps_site_identical(): void
    {
        $before = $this->snapshot();
        $this->actingAs($this->user('Admin'));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        foreach (self::CONTENT as $resource) {
            $edit = $resource::getPages()['edit']->getPage();
            foreach ($resource::getEloquentQuery()->get() as $record) {
                Livewire::test($edit, ['record' => $record->getRouteKey()])
                    ->call('save')->assertHasNoFormErrors();
            }
        }
        $menuEdit = Resources\MenuResource::getPages()['edit']->getPage();
        foreach (Models\Menu::all() as $menu) {
            Livewire::test($menuEdit, ['record' => $menu->getRouteKey()])->call('save')->assertHasNoFormErrors();
        }
        Livewire::test(SiteSettings::class)->call('save')->assertHasNoFormErrors();

        $after = $this->snapshot();
        foreach (self::ROUTES as $route) {
            if ($before[$route] !== $after[$route]) {       // keep both versions for inspection
                @mkdir(storage_path('logs/roundtrip'), 0777, true);
                $name = trim(str_replace('/', '_', $route), '_') ?: 'home';
                file_put_contents(storage_path("logs/roundtrip/{$name}.before.html"), $before[$route]);
                file_put_contents(storage_path("logs/roundtrip/{$name}.after.html"), $after[$route]);
            }
            $this->assertSame($before[$route], $after[$route], "Page {$route} changed after saving records unchanged");
        }
    }

    public function test_editor_create_draft_delete_itinerary_with_days(): void
    {
        $this->actingAs($this->user('Editor'));
        Livewire::test(Resources\ItineraryResource\Pages\CreateItinerary::class)
            ->fillForm([
                'title' => 'Atlas Test Journey', 'status' => 'published', 'duration_label' => '3 days', 'is_signature' => false,
                'card_route' => 'Marrakech · Imlil', 'route' => 'Marrakech · Imlil', 'card_image' => 'https://images.pexels.com/photos/1.jpeg',
                'hero_image' => 'https://images.pexels.com/photos/2.jpeg', 'hero_title' => 'Atlas Test Journey', 'meta_title' => 'Atlas Test',
                'itineraryDays' => [
                    ['title' => 'Arrive in Imlil', 'description' => '<div>Walk to the <strong>village</strong>.</div>'],
                    ['title' => 'Toubkal views', 'description' => 'Morning hike.'],
                ],
                'final_cta' => ['label' => 'Like it?', 'title' => 'Ask us', 'image' => 'https://images.pexels.com/photos/3.jpeg'],
            ])
            ->call('create')->assertHasNoFormErrors();

        $it = Models\Itinerary::where('slug', 'atlas-test-journey')->firstOrFail();      // slug filled from title
        $this->assertSame([1, 2], $it->itineraryDays->pluck('day_no')->all());
        $this->get('/itineraries/atlas-test-journey')->assertOk()
            ->assertSee('<h3>Arrive in Imlil</h3><p>Walk to the <strong>village</strong>.</p>', false)   // editor <div> unwrapped
            ->assertSee('02<small>Day</small>', false);
        $this->get('/itineraries')->assertSee('Atlas Test Journey');

        Livewire::test(Resources\ItineraryResource\Pages\EditItinerary::class, ['record' => $it->slug])
            ->fillForm(['status' => 'draft'])->call('save')->assertHasNoFormErrors();
        // the signed-in editor can preview the draft (noindex); guests get a 404
        $this->get('/itineraries/atlas-test-journey')->assertOk()->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $editor = auth()->user();
        auth()->logout();
        $this->get('/itineraries/atlas-test-journey')->assertNotFound();
        $this->get('/itineraries')->assertDontSee('Atlas Test Journey');
        $this->actingAs($editor);

        Livewire::test(Resources\ItineraryResource\Pages\EditItinerary::class, ['record' => $it->slug])
            ->callAction('delete');
        $this->assertSoftDeleted($it);
        Livewire::test(Resources\ItineraryResource\Pages\ListItineraries::class)
            ->filterTable('trashed', false)->callTableAction('restore', $it->fresh());
        $this->assertNotSoftDeleted($it);
    }

    public function test_edit_itinerary_day_and_page_section_show_on_site(): void
    {
        $this->actingAs($this->user('Editor'));

        $it = Models\Itinerary::where('slug', 'grand-tour')->firstOrFail();
        $days = $it->itineraryDays->map(fn ($d) => $d->only(['title', 'description', 'image', 'image_alt', 'overnight', 'meals']))->all();
        $days[0]['title'] = 'Arrive in Casablanca (edited)';
        Livewire::test(Resources\ItineraryResource\Pages\EditItinerary::class, ['record' => 'grand-tour'])
            ->fillForm(['itineraryDays' => $days])->call('save')->assertHasNoFormErrors();
        $this->get('/itineraries/grand-tour')->assertSee('Arrive in Casablanca (edited)');

        $page = Models\Page::where('slug', 'about')->firstOrFail();
        $sections = $page->sections;
        $sections[0]['html'] = str_replace('<section', '<section data-test="edited"', $sections[0]['html']);
        $page->update(['sections' => $sections]);     // what the Builder dehydrates to
        Livewire::test(Resources\PageResource\Pages\EditPage::class, ['record' => 'about'])
            ->call('save')->assertHasNoFormErrors();
        $this->get('/about')->assertSee('data-test="edited"', false);
        $this->assertSame($sections, $page->fresh()->sections);
    }

    public function test_crud_for_each_simple_content_resource_reflects_on_site(): void
    {
        $this->actingAs($this->user('Editor'));
        $cases = [
            [Resources\FaqResource::class, ['group' => 'home', 'status' => 'published', 'question' => 'Test FAQ question?', 'answer' => '<p>Yes.</p>'], '/', 'Test FAQ question?'],
            [Resources\ExperienceResource::class, ['title' => 'Test sunset ride', 'status' => 'published', 'category' => ['desert'], 'location' => 'Agafay', 'image' => 'https://images.pexels.com/photos/9.jpeg'], '/experiences', 'Test sunset ride'],
            [Resources\TestimonialResource::class, ['name' => 'Test Guest', 'quote' => 'Lovely.', 'placement' => 'guests', 'status' => 'published'], '/', 'Test Guest'],
            [Resources\ServiceResource::class, ['name' => 'Test service', 'status' => 'published', 'card_kicker' => 'k', 'hero_image' => 'https://images.pexels.com/photos/9.jpeg', 'hero_title' => 'Test service', 'meta_title' => 't'], '/services', 'Test service'],
            [Resources\DestinationResource::class, ['name' => 'Test Oasis', 'status' => 'published', 'best_for' => 'Tests', 'has_page' => false], '/destinations', 'Test Oasis'],
            [Resources\CaseStudyResource::class, ['title' => 'Test case', 'status' => 'published', 'card_title' => 'Test case card', 'hero_image' => 'https://images.pexels.com/photos/9.jpeg', 'hero_title' => 'Test case', 'meta_title' => 't'], '/case-studies', 'Test case card'],
            [Resources\JournalPostResource::class, ['title' => 'Test post', 'status' => 'published', 'excerpt' => 'x', 'reading_minutes' => 3, 'hero_image' => 'https://images.pexels.com/photos/9.jpeg', 'hero_title' => 'Test post', 'meta_title' => 't'], '/journal', 'Test post'],
            [Resources\PageResource::class, ['title' => 'Test page', 'slug' => 'test-page', 'template' => 'default', 'status' => 'published'], null, null],
            [Resources\PartnerResource::class, ['name' => 'Test partner', 'status' => 'published'], null, null],
        ];
        foreach ($cases as [$resource, $data, $url, $needle]) {
            $pages = $resource::getPages();
            Livewire::test($pages['create']->getPage())->fillForm($data)->call('create')->assertHasNoFormErrors();
            $record = $resource::getModel()::latest('id')->first();
            if ($url) {
                $this->get($url)->assertSee($needle);
            }
            Livewire::test($pages['edit']->getPage(), ['record' => $record->getRouteKey()])
                ->fillForm(['status' => 'draft'])->call('save')->assertHasNoFormErrors();
            if ($url) {
                $this->get($url)->assertDontSee($needle);
            }
            Livewire::test($pages['edit']->getPage(), ['record' => $record->getRouteKey()])->callAction('delete');
            $this->assertSoftDeleted($record);
        }

        // category (no status): create + delete
        Livewire::test(Resources\JournalCategoryResource\Pages\CreateJournalCategory::class)
            ->fillForm(['name' => 'Test topic'])->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('journal_categories', ['slug' => 'test-topic']);
    }

    public function test_image_upload_stores_path_and_site_uses_it(): void
    {
        Storage::fake('public');
        $this->actingAs($this->user('Editor'));
        Livewire::test(Resources\ExperienceResource\Pages\EditExperience::class, ['record' => 'private-dinner-in-the-desert'])
            ->set('data.image__upload', [UploadedFile::fake()->image('dinner.jpg', 800, 600)])
            ->call('save')->assertHasNoFormErrors();
        $exp = Models\Experience::where('slug', 'private-dinner-in-the-desert')->first();
        $this->assertStringStartsWith('uploads/', $exp->image);
        Storage::disk('public')->assertExists($exp->image);
        $this->get('/experiences')->assertSee(Storage::disk('public')->url($exp->image));
    }

    public function test_menu_item_and_settings_edit_show_on_site(): void
    {
        $this->actingAs($this->user('Admin'));
        $menu = Models\Menu::where('name', 'main')->first();
        Livewire::test(Resources\MenuResource\RelationManagers\ItemsRelationManager::class, [
            'ownerRecord' => $menu, 'pageClass' => Resources\MenuResource\Pages\EditMenu::class,
        ])->callTableAction('create', data: ['label' => 'Test link', 'route' => 'contact'])->assertHasNoTableActionErrors();
        $this->get('/about')->assertSee('>Test link</a>', false);

        Livewire::test(SiteSettings::class)->fillForm(['phone_display' => '+212 999 TEST'])->call('save')->assertHasNoFormErrors();
        $this->get('/about')->assertSee('+212 999 TEST');
    }

    public function test_sales_works_leads_but_cannot_create_them(): void
    {
        $q = Models\QuoteRequest::create(['name' => 'Ann Lead', 'email' => 'ann@example.com', 'intent' => 'custom', 'note' => 'Hi']);
        $this->actingAs($this->user('Sales'));
        $this->assertFalse(Resources\QuoteRequestResource::canCreate());
        $this->assertSame('1', Resources\QuoteRequestResource::getNavigationBadge());
        Livewire::test(Resources\QuoteRequestResource\Pages\ListQuoteRequests::class)
            ->assertCanSeeTableRecords([$q])->assertTableActionExists('export')
            ->filterTable('status', ['new'])->assertCanSeeTableRecords([$q]);
        Livewire::test(Resources\QuoteRequestResource\Pages\EditQuoteRequest::class, ['record' => $q->id])
            ->fillForm(['status' => 'contacted', 'admin_notes' => 'Called', 'name' => 'Changed'])
            ->call('save')->assertHasNoFormErrors();
        $q->refresh();
        $this->assertSame(['contacted', 'Called', 'Ann Lead'], [$q->status, $q->admin_notes, $q->name]);   // submitted data stays read-only
        $this->assertNull(Resources\QuoteRequestResource::getNavigationBadge());
    }
}
