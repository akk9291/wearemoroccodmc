@extends('layouts.app')


@section('meta')
@include('partials.seo', ['m' => $itinerary])
@endsection

@php($arrow = \App\Support\Content::icon('arrow'))
@php($shape = '<button type="button" class="btn btn-gold " data-inquiry="'.e($itinerary->inquiry_key).'"><span>Shape this journey</span>'.$arrow.'</button>')

@section('content')
@include('partials.hero', ['m' => $itinerary, 'extra' => '<div class="ctas">'.$shape.'</div><dl class="facts"><div><span>Length</span><b>'.e($itinerary->duration_label).'</b></div><div><span>Route</span><b>'.$itinerary->route.'</b></div></dl>'])
<section class="sec bg-ivory"><div class="container cols c-8-4 items-start">
  <div><div class="reveal"><p class="eyebrow">Day by day</p><h2 class="h2 big">The route, <span class="it">day by day.</span></h2></div><div class="mt-14"><ol class="days">@foreach($itinerary->itineraryDays as $day)<li class="day reveal"><span class="d">{{ str_pad($day->day_no, 2, '0', STR_PAD_LEFT) }}<small>Day</small></span><div><h3>{!! $day->title !!}</h3><p>{!! \App\Support\Content::inline($day->description) !!}</p></div>@if($day->image)<div class="thumb"><img src="{{ \App\Support\Content::img($day->image) }}" alt="{{ $day->image_alt }}" loading="lazy" decoding="async"></div>@endif</li>@endforeach</ol></div>
  @if($itinerary->character)<p class="lead" style="max-width:44rem;margin-top:2.5rem"><span class="it" style="color:var(--clay)">Journey character.</span> {!! $itinerary->character !!}</p>@endif</div>
  <aside class="sticky reveal-right">
@if($itinerary->route_stops)
    <div class="box"><span class="kicker">The route</span><ol class="route-list">@foreach($itinerary->route_stops as $stop)<li>{!! $stop !!}</li>@endforeach</ol></div>
@endif
    <div class="box dark-card" style="background:var(--ink)"><h3>Signature does not mean fixed</h3><p>This journey is a starting point. We adapt the pace, houses and experiences to each traveler, or design a fully tailor-made route.</p>{!! $shape !!}</div>
  </aside>
</div></section>
<section class="sec bg-parch"><div class="container grid g-2" style="gap:1.5rem">
  <article class="vcard reveal"><span class="ico">{!! \App\Support\Content::icon('check') !!}</span><h3>Typically included</h3><ul class="dots" style="margin-top:1.25rem">@foreach($itinerary->includes ?? [] as $line)<li>{!! $line !!}</li>@endforeach</ul></article>
  <article class="vcard reveal" style="transition-delay:80ms"><span class="ico">{!! \App\Support\Content::icon('doc') !!}</span><h3>Typically excluded unless stated</h3><ul class="dots x" style="margin-top:1.25rem">@foreach($itinerary->excludes ?? [] as $line)<li>{!! $line !!}</li>@endforeach</ul></article>
</div></section>
<section class="sec bg-ivory" style="padding-top:0"><div class="container"><div class="reveal" style="padding-top:6rem"><p class="eyebrow">Along the way</p></div><div class="mt-6"><div class="gallery">@foreach($itinerary->gallery ?? [] as $g)<figure class="reveal-scale" style="transition-delay:{{ $loop->index * 70 }}ms"><img src="{{ \App\Support\Content::img($g['image']) }}" alt="{{ $g['alt'] }}" loading="lazy" decoding="async"></figure>@endforeach</div></div></div></section>
@if($itinerary->final_cta)
@include('blocks.cta', ['b' => $itinerary->final_cta])
@endif
@include('partials.sections', ['sections' => $itinerary->sections])
@endsection
