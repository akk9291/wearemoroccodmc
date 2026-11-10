<header class="site-header">
  <div class="container bar">
    <a class="brand" href="{{ route('home') }}" aria-label="We Are Morocco, home"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg><span class="word">weare<i>morocco</i></span></a>
    <nav class="main-nav" aria-label="Main">@foreach($menus['main'] as $item)<a href="{{ $item->href() }}"@if($item->isActive()) aria-current="page"@endif>{{ $item->label }}</a>@endforeach</nav>
    <div class="hdr-right">
      <a class="hdr-phone" href="tel:{{ $site['phone'] }}">{{ $site['phone_display'] }}</a>
      <button type="button" class="btn btn-gold btn-xs" data-inquiry="">Begin</button>
      <button type="button" class="burger" aria-label="Menu" aria-expanded="false" aria-controls="mnav"><svg class="lines" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 7h16M4 12h16M8 17h12"/></svg><svg class="x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
    </div>
  </div>
  <div class="mnav" id="mnav"><div class="container"><nav aria-label="Mobile">@foreach($menus['mobile'] as $item)<a href="{{ $item->href() }}"@if($item->isActive()) aria-current="page"@endif>{{ $item->label }}</a>@endforeach</nav>
    <button type="button" class="btn btn-gold" data-inquiry=""><span>Design your journey</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
    <p class="meta"><a href="tel:{{ $site['phone'] }}">{{ $site['phone_display'] }}</a> · WhatsApp</p></div></div>
</header>
