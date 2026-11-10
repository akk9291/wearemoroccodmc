<section class="final grain">
  <div class="media"><img src="{{ \App\Support\Content::img($b['image']) }}" alt="{{ $b['image_alt'] }}" loading="lazy" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true" style="left:50%;top:10%"></span>
  <div class="container narrow reveal">
    <div class="star-rule" aria-hidden="true">{!! \App\Support\Content::icon('star') !!}</div>
    <span class="label">{!! $b['label'] !!}</span>
    <h2 class="h2">{!! $b['title'] !!}</h2>
    <p class="lead">{!! $b['lead'] !!}</p>
    <div class="ctas">{!! \App\Support\Content::render($b['ctas']) !!}</div>
  </div>
</section>
