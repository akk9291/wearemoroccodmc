<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\JournalPost;
use App\Models\Page;
use App\Models\Service;
use App\Support\ContentCache;
use App\Support\Seo;
use Illuminate\Http\Response;

/** sitemap.xml (published records + pages, cached until content changes) and robots.txt. */
class SeoController extends Controller
{
    /** Pages whose slug is a section index route ("services" => services.index). */
    public const SECTION_PAGES = ['services', 'destinations', 'itineraries', 'case-studies', 'journal'];

    public function sitemap(): Response
    {
        $xml = ContentCache::remember('sitemap.xml', fn () => view('seo.sitemap', ['urls' => $this->urls()])->render());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $lines = ['User-agent: *'];
        if (Seo::indexingAllowed()) {
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /forms';
            $lines[] = 'Disallow: /livewire';
        } else {
            $lines[] = 'Disallow: /'; // local, staging and SEO_INDEXING=false (temporary domain) stay out of search engines
        }
        $lines[] = '';
        $lines[] = 'Sitemap: '.route('sitemap');

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /** @return array<int, array{loc: string, lastmod: ?string}> */
    public function urls(): array
    {
        $urls = [];
        $add = function (string $loc, $model) use (&$urls) {
            $urls[] = ['loc' => $loc, 'lastmod' => ($model->updated_at ?? $model->created_at)?->toAtomString()];
        };

        foreach (Page::published()->orderBy('id')->get() as $page) {
            if (in_array($page->slug, Seo::NOINDEX_PAGES, true)) {
                continue;
            }
            $route = in_array($page->slug, self::SECTION_PAGES, true) ? "{$page->slug}.index" : $page->slug;
            if (app('router')->has($route)) {
                $add(route($route), $page);
            }
        }
        foreach (Service::published()->ordered()->get() as $m) {
            $add(route('services.show', $m), $m);
        }
        foreach (Destination::published()->where('has_page', true)->ordered()->get() as $m) {
            $add(route('destinations.show', $m), $m);
        }
        foreach (Itinerary::published()->ordered()->get() as $m) {
            $add(route('itineraries.show', $m), $m);
        }
        foreach (CaseStudy::published()->ordered()->get() as $m) {
            $add(route('case-studies.show', $m), $m);
        }
        foreach (JournalPost::published()->ordered()->get() as $m) {
            $add(route('journal.show', $m), $m);
        }

        return $urls;
    }
}
