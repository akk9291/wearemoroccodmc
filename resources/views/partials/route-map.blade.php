{{-- Route map block for an itinerary. Bundled maps (/assets/img/maps/<name>-2400.jpg) get WebP 800/1600/2400 variants; uploads use the normal image variants. --}}
@php
    $map = $itinerary->route_map;
    $alt = $itinerary->route_map_alt ?: 'Route map of '.strip_tags($itinerary->title);
    $large = \App\Support\Content::img($map);
    $webp = null;
    if (preg_match('#^/assets/img/maps/(.+)-2400\.jpg$#', $map, $m)) {
        $webp = collect([800, 1600, 2400])
            ->filter(fn ($w) => is_file(public_path("assets/img/maps/{$m[1]}-{$w}.webp")))
            ->map(fn ($w) => asset("assets/img/maps/{$m[1]}-{$w}.webp")." {$w}w")->implode(', ');
    }
    $sizes = '(min-width: 1280px) 780px, (min-width: 1024px) 60vw, 100vw';
@endphp
@push('head')
<link rel="stylesheet" href="{{ \App\Support\Content::asset('assets/route-map.css') }}">
@endpush
<figure class="route-map reveal">
  <a href="{{ $large }}" target="_blank" rel="noopener" class="route-map-link" aria-label="Open the large route map (opens in a new tab)">
    <picture>
      @if($webp)<source type="image/webp" srcset="{{ $webp }}" sizes="{{ $sizes }}">@endif
      <img src="{{ $large }}"{!! $webp ? '' : \App\Support\ImageVariants::attributes($map, $sizes) !!} alt="{{ $alt }}" width="2400" height="1600" loading="lazy" decoding="async">
    </picture>
    <span class="route-map-zoom" aria-hidden="true">{!! \App\Support\Content::icon('arrow') !!}</span>
  </a>
  <figcaption><span class="kicker">Route map</span><span class="route-map-hint">Open the large map</span></figcaption>
</figure>
