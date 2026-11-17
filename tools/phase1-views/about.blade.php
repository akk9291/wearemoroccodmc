@extends('layouts.app')

@section('nav', 'about')
@section('title')
The Atelier | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="A Marrakech atelier for private journeys and events: who we are, how we work and the team on the ground.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="The Atelier | We Are Morocco">
<meta property="og:description" content="A Marrakech atelier for private journeys and events: who we are, how we work and the team on the ground.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain">
  <div class="media"><img src="https://images.pexels.com/photos/27945049/pexels-photo-27945049.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Palace courtyard with fountain and arches" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">The Atelier</li></ol><p class="eyebrow">About the atelier</p>
    <h1 class="h1" style="margin-top:1.25rem">We are <span class="it">Morocco.</span></h1>
    <p class="lead">A destination atelier born and based in Marrakech, with partners in every region of the country and fifty years of combined work on this ground.</p>
    
  </div>
</section>
<section class="sec bg-parch">
  <div class="container cols c-1-1 items-center">
    <div class="feature reveal-left"><span class="ring" aria-hidden="true"></span><div class="frame img-zoom"><img src="https://images.pexels.com/photos/50630/pexels-photo-50630.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="A guest looking out over Marrakech at dusk from a private terrace" loading="lazy" decoding="async"></div><div class="badge"><b>Since [year]</b><span>Showing Morocco the way locals know it.</span></div></div>
    <div class="reveal-right"><p class="eyebrow">Our story</p><h2 class="h2 big">Founded by a local. <span class="it">Run by people who live here.</span></h2>
      <p class="lead">We Are Morocco was founded in <span class="ph" title="Placeholder: year">[year]</span> by <span class="ph" title="Placeholder: founder">[founder name]</span>, who grew up in <span class="ph" title="Placeholder: city">[city]</span> and spent years in hospitality before deciding to show Morocco the way locals know it.</p>
      <p class="lead">Today our producers, guides and coordinators work from our atelier in Marrakech — so when something shifts, we are already in the medina, not in another time zone.</p>
      <div class="ctas"><button type="button" class="btn btn-gold " data-inquiry=""><span>Begin a conversation</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button><a class="btn btn-outline" href="{{ route('why-us') }}">Why travelers choose us</a></div></div>
  </div>
</section>
<section class="sec bg-ink center">
  <span class="orb orb-soft" aria-hidden="true"></span>
  <div class="container narrow reveal"><p class="eyebrow">Our mission</p><h2 class="h2 big" style="margin-top:1.25rem">Effortless for our guests. <span class="it block">Unforgettable for everyone they bring.</span></h2>
    <p class="lead">To make every journey, event and gathering in Morocco feel privately edited — quiet logistics, the right doors opened, and nothing that feels like a production.</p></div>
</section>
<section class="sec bg-ivory">
  <div class="container"><div class="head-row"><div class="reveal"><p class="eyebrow">The team</p><h2 class="h2 big">The people <span class="it">on the ground.</span></h2></div><p class="lead reveal" style="transition-delay:120ms">Producers, guides and designers who answer on your clock. Photos and names are added in the admin.</p></div>
    <div class="grid g-4 mt-14"><article class="member reveal" style="transition-delay:0ms"><div class="ph-face"><span class="display" style="font-size:3.5rem;color:var(--gold-light);font-weight:300">A</span></div>
<h3>[Name]</h3><p class="kicker role">Founder</p><p>“[Personal line, for example the city they grew up in]”</p></article><article class="member reveal" style="transition-delay:80ms"><div class="ph-face"><span class="display" style="font-size:3.5rem;color:var(--gold-light);font-weight:300">B</span></div>
<h3>[Name]</h3><p class="kicker role">Head of events</p><p>“[Personal line]”</p></article><article class="member reveal" style="transition-delay:160ms"><div class="ph-face"><span class="display" style="font-size:3.5rem;color:var(--gold-light);font-weight:300">C</span></div>
<h3>[Name]</h3><p class="kicker role">Journey producer</p><p>“Knows every back road between Marrakech and Merzouga.”</p></article><article class="member reveal" style="transition-delay:240ms"><div class="ph-face"><span class="display" style="font-size:3.5rem;color:var(--gold-light);font-weight:300">D</span></div>
<h3>[Name]</h3><p class="kicker role">Travel designer</p><p>“[Personal line]”</p></article></div></div>
</section>
<section class="sec bg-parch">
  <div class="container"><div class="head-row"><div class="reveal"><p class="eyebrow">Credentials</p><h2 class="h2 big">Licensed, insured <span class="it">and accountable.</span></h2></div></div>
    <div class="mt-14"><div class="grid g-3"><article class="vcard lift reveal" style="transition-delay:0ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6z"/><path d="m9 12 2 2 4-4"/></svg></span><h3>Ministry of Tourism licence</h3><p>Licence number <span class="ph" title="Placeholder: licence">[ ]</span>.</p></article><article class="vcard lift reveal" style="transition-delay:80ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4 10-10"/></svg></span><h3>Insurance and safety</h3><p>Full liability insurance, vetted drivers, tracked vehicles and a private medical network.</p></article><article class="vcard lift reveal" style="transition-delay:160ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg></span><h3>Memberships</h3><p>Industry memberships, for example Virtuoso or ADMEI, once confirmed.</p></article></div></div>
    <div class="logos"><span>Membership logo</span><span>Membership logo</span><span>Membership logo</span><span>Licence badge</span></div></div>
</section>
<section class="final grain">
  <div class="media"><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" loading="lazy" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true" style="left:50%;top:10%"></span>
  <div class="container narrow reveal">
    <div class="star-rule" aria-hidden="true"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg></div>
    <span class="label">Meet the atelier</span>
    <h2 class="h2">Want to meet the team? <span class="it block">Start with a conversation.</span></h2>
    <p class="lead">Tell us what you are planning and the right producer will reply within one business day.</p>
    <div class="ctas"><button type="button" class="btn btn-gold " data-inquiry=""><span>Design your journey</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button><a class="btn btn-ghost" href="mailto:info@wearemoroccodmc.com">info@wearemoroccodmc.com</a></div>
  </div>
</section>
@endsection
