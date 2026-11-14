<?php

namespace App\Support;

use App\Models\CaseStudy;
use App\Models\Destination;
use App\Models\Faq;
use App\Models\Itinerary;
use App\Models\JournalPost;
use App\Models\Page;
use App\Models\Service;
use App\Models\Setting;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Everything the <head> needs for one page: title, description, canonical, robots, Open Graph / Twitter, JSON-LD.
 * Values come from the record's meta_* columns, falling back to its title/excerpt and the SEO defaults in Settings.
 * URLs always come from url()/route(), so they follow APP_URL.
 */
class Seo
{
    /** Page slugs that must never be indexed. */
    public const NOINDEX_PAGES = ['thank-you'];

    /** Detail models => [index route, section label] for the breadcrumb fallback. */
    public const SECTIONS = [
        Service::class => ['services.index', 'Services'],
        Destination::class => ['destinations.index', 'Destinations'],
        Itinerary::class => ['itineraries.index', 'Journeys'],
        CaseStudy::class => ['case-studies.index', 'Case studies'],
        JournalPost::class => ['journal.index', 'Journal'],
    ];

    public string $title;

    public string $description;

    public string $canonical;

    public string $image;

    public string $robots;

    public string $type;

    /** @var array<int, array> JSON-LD nodes */
    public array $schema = [];

    public static function for(?Model $m = null, array $overrides = []): self
    {
        $seo = new self;
        $site = Setting::allValues();
        $siteName = $site['site_name'] ?? 'We Are Morocco';

        $name = $m ? static::plain($m->getAttribute('title') ?? $m->getAttribute('name')) : null;
        $seo->title = static::plain($m?->getAttribute('meta_title')) ?: ($name ? "{$name} | {$siteName}" : $siteName);
        $seo->description = static::plain($m?->getAttribute('meta_description'))
            ?: static::excerpt($m?->getAttribute('excerpt') ?? $m?->getAttribute('summary') ?? $m?->getAttribute('intro') ?? $m?->getAttribute('card_summary'))
            ?: static::plain($site['default_meta_description'] ?? '');
        $seo->canonical = url()->current();
        $seo->image = Content::img($m?->getAttribute('og_image') ?: $m?->getAttribute('hero_image') ?: $m?->getAttribute('cover_image')
            ?: $m?->getAttribute('card_image') ?: ($site['default_og_image'] ?? null));
        $seo->type = $m instanceof JournalPost ? 'article' : 'website';

        $draft = $m && $m->getAttribute('status') !== null && $m->getAttribute('status') !== 'published';
        $noindex = $draft || ($m instanceof Page && in_array($m->slug, self::NOINDEX_PAGES, true));
        $seo->robots = $noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large';

        foreach ($overrides as $key => $value) {
            $seo->{$key} = $value;
        }

        $seo->schema = $seo->buildSchema($m, $site, $siteName);

        return $seo;
    }

    /** Signed-in team members (admin panel access) may preview drafts; the page is then noindex. */
    public static function canPreviewDrafts(): bool
    {
        $user = auth()->user();

        return $user !== null && method_exists($user, 'canAccessPanel') && $user->canAccessPanel(Filament::getPanel('admin'));
    }

    public function jsonLd(): string
    {
        return json_encode(['@context' => 'https://schema.org', '@graph' => $this->schema],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
    }

    private function buildSchema(?Model $m, array $site, string $siteName): array
    {
        $orgId = url('/').'#organization';
        $graph = [static::organization($site, $siteName, $orgId)];

        if ($m && isset(self::SECTIONS[$m::class])) {
            $graph[] = $this->breadcrumbs($m);
        }
        if ($m instanceof Itinerary) {
            $graph[] = $this->touristTrip($m, $orgId);
        }
        if ($m instanceof JournalPost) {
            $graph[] = $this->article($m, $orgId);
        }
        if ($m instanceof Page && $m->slug === 'faq') {
            $graph[] = $this->faqPage($m);
        }

        return $graph;
    }

    public static function organization(array $site, string $siteName, string $id): array
    {
        $logo = $site['logo'] ?? null;
        $sameAs = collect($site['socials'] ?? [])->pluck('url')->filter(fn ($u) => is_string($u) && preg_match('#^https?://#', $u))->values()->all();

        return array_filter([
            '@type' => 'TravelAgency',
            '@id' => $id,
            'name' => $siteName,
            'url' => url('/'),
            'logo' => $logo ? Content::img($logo) : asset('assets/favicon.svg'),
            'image' => Content::img($site['default_og_image'] ?? null) ?: null,
            'description' => static::plain($site['default_meta_description'] ?? '') ?: null,
            'telephone' => $site['phone'] ?? null,
            'email' => $site['email'] ?? null,
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $site['address'] ?? null,
                'addressLocality' => $site['address_city'] ?? null,
                'addressCountry' => $site['address_country'] ?? null,
            ]),
            'sameAs' => $sameAs ?: null,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    private function breadcrumbs(Model $m): array
    {
        // Linked crumbs from the hero (Home > Journal ...), else Home > section; always ending on this page.
        $crumbs = collect($m->getAttribute('hero_crumbs') ?? [])->filter(fn ($c) => ! empty($c['url']))->map(fn ($c) => [
            'name' => static::plain($c['label'] ?? ''), 'url' => url($c['url']),
        ])->filter(fn ($c) => $c['name'] !== '')->values();
        if ($crumbs->isEmpty()) {
            [$route, $label] = self::SECTIONS[$m::class];
            $crumbs = collect([['name' => 'Home', 'url' => url('/')], ['name' => $label, 'url' => route($route)]]);
        }
        $crumbs->push(['name' => static::plain($m->getAttribute('title') ?? $m->getAttribute('name')), 'url' => null]);

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $crumbs->map(fn ($c, $i) => [
                '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name'], 'item' => $c['url'] ?? $this->canonical,
            ])->all(),
        ];
    }

    private function touristTrip(Itinerary $it, string $orgId): array
    {
        $days = $it->relationLoaded('itineraryDays') ? $it->itineraryDays : $it->itineraryDays()->get();
        $trip = [
            '@type' => 'TouristTrip',
            'name' => static::plain($it->title),
            'description' => static::excerpt($it->summary) ?: $this->description,
            'url' => $this->canonical,
            'image' => $this->image ?: null,
            'provider' => ['@id' => $orgId],
            'itinerary' => [
                '@type' => 'ItemList',
                'numberOfItems' => $days->count(),
                'itemListElement' => $days->values()->map(fn ($d, $i) => array_filter([
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => 'Day '.$d->day_no.': '.static::plain($d->title),
                    'description' => static::excerpt($d->description, 300) ?: null,
                ]))->all(),
            ],
        ];
        if ($it->price_from) {
            $trip['offers'] = ['@type' => 'Offer', 'price' => (string) $it->price_from, 'priceCurrency' => $it->currency ?: 'EUR', 'url' => $this->canonical];
        }

        return array_filter($trip, fn ($v) => $v !== null && $v !== '');
    }

    private function article(JournalPost $p, string $orgId): array
    {
        return array_filter([
            '@type' => 'Article',
            'headline' => mb_substr(static::plain($p->title), 0, 110),
            'description' => $this->description ?: null,
            'image' => $this->image ? [$this->image] : null,
            'datePublished' => ($p->published_at ?? $p->created_at)?->toAtomString(),
            'dateModified' => ($p->updated_at ?? $p->created_at)?->toAtomString(),
            'author' => $p->author && ! str_contains($p->author, '[') ? ['@type' => 'Person', 'name' => static::plain($p->author)] : ['@id' => $orgId],
            'publisher' => ['@id' => $orgId],
            'mainEntityOfPage' => $this->canonical,
        ], fn ($v) => $v !== null && $v !== '');
    }

    /** The FAQs shown on the page: the groups of its <!--wam:faqs group="..."--> blocks, each question once. */
    private function faqPage(Page $page): array
    {
        $html = collect($page->sections ?? [])->pluck('html')->filter()->implode("\n");
        preg_match_all('/<!--wam:faqs group="([^"]+)"/', $html, $m);
        $groups = array_values(array_unique($m[1]));
        $faqs = Faq::published()->whereIn('group', $groups)->ordered()->get()
            ->sortBy(fn ($f) => array_search($f->group, $groups, true), SORT_REGULAR)->unique(fn ($f) => static::plain($f->question));

        return [
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn ($f) => [
                '@type' => 'Question',
                'name' => static::plain($f->question),
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => static::plain($f->answer)],
            ])->values()->all(),
        ];
    }

    /** HTML or entity-encoded text -> one line of plain text. */
    public static function plain(?string $value): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    public static function excerpt(?string $value, int $limit = 160): string
    {
        $text = static::plain($value);

        return mb_strlen($text) > $limit ? rtrim(mb_substr($text, 0, $limit - 1)).'…' : $text;
    }
}
