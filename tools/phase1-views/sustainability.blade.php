@extends('layouts.app')

@section('nav', 'about')
@section('title')
Sustainability | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Responsible travel in Morocco: family-run riads, cooperatives, community guides, less plastic and lower-carbon transport.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Sustainability | We Are Morocco">
<meta property="og:description" content="Responsible travel in Morocco: family-run riads, cooperatives, community guides, less plastic and lower-carbon transport.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain">
  <div class="media"><img src="https://images.pexels.com/photos/37441764/pexels-photo-37441764.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Palm oasis and kasbah walls" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('about') }}">The Atelier</a></li><li aria-current="page">Sustainability</li></ol><p class="eyebrow">Sustainability & responsible travel</p>
    <h1 class="h1" style="margin-top:1.25rem">Travel that <span class="it">gives back to Morocco.</span></h1>
    <p class="lead">More of your budget stays in the places you visit.</p>
    
  </div>
</section>
<section class="sec bg-parch"><div class="container cols c-1-1 items-center">
  <div class="feature reveal-left"><span class="ring" aria-hidden="true"></span><div class="frame img-zoom"><img src="https://images.pexels.com/photos/37684084/pexels-photo-37684084.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1800" alt="The earthen kasbah of Aït Benhaddou" loading="lazy" decoding="async"></div><div class="badge"><b>Local first</b><span>Family-run riads, cooperatives and community guides.</span></div></div>
  <div class="reveal-right"><p class="eyebrow">Our approach</p><h2 class="h2 big">Responsible <span class="it">by design.</span></h2><p class="lead">We work with family-run riads, local cooperatives and community guides, so more of your budget stays in the places you visit. We cut single-use plastic on our journeys, offer lower-carbon transport options and help planners add a social impact moment to any itinerary.</p></div>
</div></section>
<section class="sec bg-ivory"><div class="container"><div class="head-row"><div class="reveal"><p class="eyebrow">Commitments</p><h2 class="h2 big">What we <span class="it">commit to.</span></h2></div></div>
  <div class="mt-14"><div class="grid g-4"><article class="vcard lift reveal" style="transition-delay:0ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 19c0-8 5-14 15-14 0 10-6 15-14 15"/><path d="M5 19 13 11"/></svg></span><h3>Less plastic</h3><p>Refillable bottles and no single-use plastic on our journeys.</p></article><article class="vcard lift reveal" style="transition-delay:80ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 16v-4l2-5h14l2 5v4H3z"/><circle cx="7.5" cy="16.5" r="1.5"/><circle cx="16.5" cy="16.5" r="1.5"/><path d="M3 12h18"/></svg></span><h3>Lower-carbon transport</h3><p>Rail between cities where it works, and efficient vehicles.</p></article><article class="vcard lift reveal" style="transition-delay:160ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M21.5 20a6.5 6.5 0 0 0-4-6"/></svg></span><h3>Local partners</h3><p>Cooperatives we work with: <span class="ph" title="Placeholder">[names]</span>.</p></article><article class="vcard lift reveal" style="transition-delay:240ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="8" width="18" height="13" rx="1"/><path d="M3 12h18M12 8v13M12 8S10 3 7.5 4.5 9 8 12 8zM12 8s2-5 4.5-3.5S15 8 12 8z"/></svg></span><h3>Social impact</h3><p>A community moment can be added to any itinerary.</p></article></div></div>
  <p class="fine" style="text-align:left">To confirm: partner cooperatives, carbon policy, charity partners and certification (for example Travelife).</p>
  <div class="logos"><span>Partner logo</span><span>Partner logo</span><span>Certification</span></div></div></section>
<section class="final grain">
  <div class="media"><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" loading="lazy" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true" style="left:50%;top:10%"></span>
  <div class="container narrow reveal">
    <div class="star-rule" aria-hidden="true"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg></div>
    <span class="label">Lower impact</span>
    <h2 class="h2">Want a lower-impact journey? <span class="it block">Ask us how.</span></h2>
    <p class="lead">We can build sustainability into your brief from the very first sketch.</p>
    <div class="ctas"><button type="button" class="btn btn-gold " data-inquiry=""><span>Design your journey</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button><a class="btn btn-ghost" href="mailto:info@wearemoroccodmc.com">info@wearemoroccodmc.com</a></div>
  </div>
</section>
@endsection
