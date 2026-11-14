{{-- Page hero from the hero_* columns. $extra (optional HTML) replaces hero_extra. --}}
<section class="phero grain{{ $m->hero_variant ? ' '.$m->hero_variant : '' }}">
  <div class="media"><img src="{{ \App\Support\Content::img($m->hero_image) }}"{!! \App\Support\ImageVariants::attributes($m->hero_image, '100vw') !!} alt="{{ $m->hero_image_alt }}" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    @if($m->hero_crumbs)<ol class="crumbs">{!! \App\Support\Content::crumbs($m->hero_crumbs) !!}</ol>@endif<p class="eyebrow">{!! $m->hero_eyebrow !!}</p>
    <h1 class="h1" style="margin-top:1.25rem">{!! $m->hero_title !!}</h1>
    @if($m->hero_subtitle !== null)<p class="lead">{!! $m->hero_subtitle !!}</p>@endif
    {!! $extra ?? \App\Support\Content::render($m->hero_extra) !!}
  </div>
</section>
