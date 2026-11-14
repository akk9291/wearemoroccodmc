<?php

namespace Tests\Feature;

use App\Models;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapRobotsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @return array<string, string> loc => lastmod */
    private function sitemap(): array
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'well-formed XML');
        $this->assertSame('http://www.sitemaps.org/schemas/sitemap/0.9', $xml->getNamespaces()['']);

        $urls = [];
        foreach ($xml->url as $url) {
            $urls[(string) $url->loc] = (string) $url->lastmod;
        }

        return $urls;
    }

    public function test_sitemap_lists_every_public_page_except_thank_you(): void
    {
        $urls = $this->sitemap();

        $expected = collect(AdminPanelTest::ROUTES)->reject(fn ($u) => $u === '/thank-you')->map(fn ($u) => url($u))->sort()->values()->all();
        $this->assertSame($expected, collect(array_keys($urls))->sort()->values()->all());

        $post = Models\JournalPost::where('slug', 'best-time-to-visit-marrakech')->firstOrFail();
        $this->assertSame($post->updated_at->toAtomString(), $urls[url('/journal/best-time-to-visit-marrakech')]);
    }

    public function test_sitemap_is_cached_and_refreshed_when_content_changes(): void
    {
        $this->sitemap();

        // A raw query does not fire model events: the cached sitemap still lists the itinerary.
        \DB::table('itineraries')->where('slug', 'grand-tour')->update(['status' => 'draft']);
        $this->assertArrayHasKey(url('/itineraries/grand-tour'), $this->sitemap());

        // Saving through the model clears it.
        Models\Itinerary::where('slug', 'desert-kasbahs')->firstOrFail()->update(['status' => 'draft']);
        $urls = $this->sitemap();
        $this->assertArrayNotHasKey(url('/itineraries/grand-tour'), $urls);
        $this->assertArrayNotHasKey(url('/itineraries/desert-kasbahs'), $urls);
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $body = $this->get('/robots.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')->getContent();

        $this->assertStringContainsString("User-agent: *\nDisallow: /\n", $body);
        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $body);
    }

    public function test_robots_in_production_allows_the_site_but_not_admin_or_forms(): void
    {
        $this->app['env'] = 'production';
        $body = $this->get('/robots.txt')->assertOk()->getContent();
        $this->app['env'] = 'testing';

        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Disallow: /forms', $body);
        $this->assertDoesNotMatchRegularExpression('#^Disallow: /$#m', $body);
        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $body);
    }
}
