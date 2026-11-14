@php($journeys = \App\Support\ContentCache::remember('script.journeys', fn () => \App\Models\Itinerary::published()->signature()->ordered()->get()->map(fn ($it) => [
    'name' => $it->title,
    'img' => \App\Support\Content::img($it->card_image),
    'route' => html_entity_decode((string) $it->card_route, ENT_QUOTES | ENT_HTML5),
    'summary' => html_entity_decode((string) $it->summary, ENT_QUOTES | ENT_HTML5),
    'includes' => $it->highlights ?? [],
    'href' => route('itineraries.show', $it),
    'cta' => 'See the day-by-day',
])->all()))
<script>window.WAM_JOURNEYS={!! json_encode($journeys, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!};</script>
