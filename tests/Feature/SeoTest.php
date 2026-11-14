<?php

namespace Tests\Feature;

use App\Models;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        config(['livewire.inject_assets' => false]);
    }

    /** @return array<int, array> JSON-LD @graph nodes of a page (fails when missing or not valid JSON) */
    private function graph(string $html): array
    {
        $this->assertSame(1, preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m), 'one JSON-LD block');
        $data = json_decode($m[1][0], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('https://schema.org', $data['@context']);

        return $data['@graph'];
    }

    private function node(array $graph, string $type): ?array
    {
        return collect($graph)->firstWhere('@type', $type);
    }

    private function meta(string $html, string $attr, string $name): ?string
    {
        return preg_match('#<meta '.$attr.'="'.preg_quote($name, '#').'" content="([^"]*)"#', $html, $m) ? html_entity_decode($m[1]) : null;
    }

    public function test_every_page_has_a_complete_seo_head(): void
    {
        foreach (AdminPanelTest::ROUTES as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();
            $this->assertSame(1, substr_count($html, '<title>'), "$uri title");
            $this->assertNotSame('', trim(strip_tags(preg_replace('#.*<title>(.*?)</title>.*#s', '$1', $html))), "$uri title text");
            $this->assertNotEmpty($this->meta($html, 'name', 'description'), "$uri description");
            $this->assertStringContainsString('<link rel="canonical" href="'.url($uri === '/' ? '' : $uri).'">', $html, "$uri canonical");
            foreach (['og:title', 'og:description', 'og:url', 'og:image', 'og:type'] as $og) {
                $this->assertNotEmpty($this->meta($html, 'property', $og), "$uri $og");
            }
            $this->assertSame('summary_large_image', $this->meta($html, 'name', 'twitter:card'));
            $this->assertSame($uri === '/thank-you' ? 'noindex, nofollow' : 'index, follow, max-image-preview:large', $this->meta($html, 'name', 'robots'), "$uri robots");

            $org = $this->node($this->graph($html), 'TravelAgency');
            $this->assertNotNull($org, "$uri organization");
            foreach (['@id', 'name', 'url', 'logo', 'telephone', 'email', 'address'] as $field) {
                $this->assertNotEmpty($org[$field] ?? null, "$uri organization $field");
            }
            $this->assertSame('PostalAddress', $org['address']['@type']);
        }
    }

    public function test_detail_pages_have_breadcrumbs_ending_on_the_page(): void
    {
        foreach (['/services/events', '/destinations/marrakech', '/itineraries/grand-tour', '/case-studies/agafay-gala',
            '/journal/best-time-to-visit-marrakech'] as $uri) {
            $crumbs = $this->node($this->graph($this->get($uri)->getContent()), 'BreadcrumbList');
            $this->assertNotNull($crumbs, $uri);
            $items = $crumbs['itemListElement'];
            $this->assertGreaterThanOrEqual(3, count($items));
            $this->assertSame('Home', $items[0]['name']);
            $this->assertSame(url($uri), end($items)['item']);
            foreach ($items as $i => $item) {
                $this->assertSame($i + 1, $item['position']);
                $this->assertNotEmpty($item['name']);
            }
        }
        $this->assertNull($this->node($this->graph($this->get('/about')->getContent()), 'BreadcrumbList'));
    }

    public function test_itinerary_is_a_tourist_trip_with_its_days(): void
    {
        $itinerary = Models\Itinerary::where('slug', 'grand-tour')->firstOrFail();
        $trip = $this->node($this->graph($this->get('/itineraries/grand-tour')->getContent()), 'TouristTrip');

        $this->assertSame('The Grand Morocco', $trip['name']);
        $this->assertNotEmpty($trip['description']);
        $this->assertSame($itinerary->itineraryDays()->count(), $trip['itinerary']['numberOfItems']);
        $this->assertCount($trip['itinerary']['numberOfItems'], $trip['itinerary']['itemListElement']);
        $this->assertStringStartsWith('Day 1: ', $trip['itinerary']['itemListElement'][0]['name']);
    }

    public function test_journal_post_is_an_article(): void
    {
        $html = $this->get('/journal/best-time-to-visit-marrakech')->getContent();
        $article = $this->node($this->graph($html), 'Article');

        foreach (['headline', 'image', 'datePublished', 'dateModified', 'author', 'publisher', 'mainEntityOfPage'] as $field) {
            $this->assertNotEmpty($article[$field] ?? null, $field);
        }
        $this->assertLessThanOrEqual(110, mb_strlen($article['headline']));
        $this->assertSame('article', $this->meta($html, 'property', 'og:type'));
    }

    public function test_faq_page_lists_the_questions_shown(): void
    {
        $html = $this->get('/faq')->getContent();
        $faq = $this->node($this->graph($html), 'FAQPage');

        $this->assertCount(substr_count($html, 'class="faq-q"'), $faq['mainEntity']);
        foreach ($faq['mainEntity'] as $q) {
            $this->assertSame('Question', $q['@type']);
            $this->assertNotEmpty($q['name']);
            $this->assertNotEmpty($q['acceptedAnswer']['text']);
        }
        $this->assertNull($this->node($this->graph($this->get('/')->getContent()), 'FAQPage'));
    }

    public function test_meta_falls_back_to_title_and_site_defaults(): void
    {
        Models\Page::where('slug', 'about')->update(['meta_title' => null, 'meta_description' => null, 'og_image' => null, 'hero_image' => null]);
        $html = $this->get('/about')->getContent();

        $title = Models\Page::where('slug', 'about')->value('title');
        $this->assertStringContainsString('<title>'.e($title).' | We Are Morocco</title>', $html);
        $this->assertSame(Models\Setting::get('default_meta_description'), $this->meta($html, 'name', 'description'));
        $this->assertSame(Models\Setting::get('default_og_image'), $this->meta($html, 'property', 'og:image'));
    }

    public function test_drafts_are_hidden_from_guests_and_previewed_noindex_by_admins(): void
    {
        Models\Service::where('slug', 'events')->update(['status' => 'draft']);
        $this->get('/services/events')->assertNotFound();

        $admin = Models\User::factory()->create();
        $admin->assignRole('Admin');
        $html = $this->actingAs($admin)->get('/services/events')->assertOk()->getContent();
        $this->assertSame('noindex, nofollow', $this->meta($html, 'name', 'robots'));
    }
}
