@extends('layouts.app')

@section('nav', 'services')
@section('title')
Case studies | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Programs we have run on the ground in Morocco: incentives, retreats, congresses and private journeys.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Case studies | We Are Morocco">
<meta property="og:description" content="Programs we have run on the ground in Morocco: incentives, retreats, congresses and private journeys.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain short">
  <div class="media"><img src="https://images.pexels.com/photos/10434667/pexels-photo-10434667.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1800" alt="Private desert camp in the Sahara" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('services.index') }}">Services</a></li><li aria-current="page">Case studies</li></ol><p class="eyebrow">Case studies</p>
    <h1 class="h1" style="margin-top:1.25rem">Programs we have <span class="it">run on the ground.</span></h1>
    <p class="lead">Client, group size, challenge, what we did and the result.</p>
    
  </div>
</section>
<section class="sec bg-parch"><div class="container">
  <div class="filters"><div class="row"><span class="kicker">Service</span><div class="chips" data-filter-group="svc" data-target="#cs-grid"><button type="button" class="chip" data-value="all" aria-pressed="true">All</button><button type="button" class="chip" data-value="incentive" aria-pressed="false">Incentives</button><button type="button" class="chip" data-value="event" aria-pressed="false">Events</button><button type="button" class="chip" data-value="private" aria-pressed="false">Private</button></div></div></div>
  <div class="grid g-2" id="cs-grid" style="gap:1.5rem"><a href="{{ route('case-studies.show', 'agafay-gala') }}" class="scard lift reveal" data-item data-svc="incentive" style="transition-delay:0ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Incentive · [120] guests</span><h3>[Client]: an Agafay gala for [120] guests</h3><p>Marrakech · Agafay · Sample</p><span class="link">Read the case study <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a><a href="{{ route('case-studies.show', 'agafay-gala') }}" class="scard lift reveal" data-item data-svc="event" style="transition-delay:80ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/15260622/pexels-photo-15260622.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Marrakech palace interior" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Conference · [600] delegates</span><h3>[Client]: a [600]-delegate congress</h3><p>Marrakech · Sample</p><span class="link">Read the case study <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a><a href="{{ route('case-studies.show', 'agafay-gala') }}" class="scard lift reveal" data-item data-svc="event" style="transition-delay:0ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/29976840/pexels-photo-29976840.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Kasbah in the Atlas foothills" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Retreat · [30] guests</span><h3>[Client]: a leadership retreat in the Atlas</h3><p>Atlas Mountains · Sample</p><span class="link">Read the case study <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a><a href="{{ route('case-studies.show', 'agafay-gala') }}" class="scard lift reveal" data-item data-svc="private" style="transition-delay:80ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/25070537/pexels-photo-25070537.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Blue alleys of Chefchaouen" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Private journey · [4] travelers</span><h3>[Family]: ten days, coast to dunes</h3><p>Fès · Sahara · Essaouira · Sample</p><span class="link">Read the case study <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a></div>
  <p class="empty" data-empty hidden>No case studies in this category yet.</p>
  <p class="fine" style="text-align:left">These cards show the layout. Real case studies replace them in the admin, with client permission.</p>
</div></section>
<section class="final grain">
  <div class="media"><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" loading="lazy" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true" style="left:50%;top:10%"></span>
  <div class="container narrow reveal">
    <div class="star-rule" aria-hidden="true"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg></div>
    <span class="label">Have a program like these?</span>
    <h2 class="h2">Let us quote it. <span class="it block">A first sketch within days.</span></h2>
    <p class="lead">Tell us the group, dates and the feeling you want guests to leave with.</p>
    <div class="ctas"><button type="button" class="btn btn-gold " data-inquiry="event"><span>Design your journey</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button><a class="btn btn-ghost" href="mailto:info@wearemoroccodmc.com">info@wearemoroccodmc.com</a></div>
  </div>
</section>
@endsection
