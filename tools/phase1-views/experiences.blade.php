@extends('layouts.app')

@section('nav', 'experiences')
@section('title')
Experiences | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Private dinners, desert camps, palace buyouts and artisan sessions in Morocco — access you cannot book online.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Experiences | We Are Morocco">
<meta property="og:description" content="Private dinners, desert camps, palace buyouts and artisan sessions in Morocco — access you cannot book online.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain">
  <div class="media"><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">Experiences</li></ol><p class="eyebrow">Signature experiences</p>
    <h1 class="h1" style="margin-top:1.25rem">Access you <span class="it">can’t book online.</span></h1>
    <p class="lead">Private venues, desert camps and artisan sessions, arranged through people we have worked with for years. Add any of them to a journey.</p>
    
  </div>
</section>
<section class="sec bg-ink"><div class="container">
  <div class="filters"><div class="row"><span class="kicker">Category</span><div class="chips" data-filter-group="cat" data-target="#exp-grid"><button type="button" class="chip" data-value="all" aria-pressed="true">All</button><button type="button" class="chip" data-value="gala" aria-pressed="false">Celebrations</button><button type="button" class="chip" data-value="desert" aria-pressed="false">Desert</button><button type="button" class="chip" data-value="culture" aria-pressed="false">Culture</button><button type="button" class="chip" data-value="food" aria-pressed="false">Food</button><button type="button" class="chip" data-value="adventure" aria-pressed="false">Adventure</button></div></div></div>
  <div class="grid g-3" id="exp-grid"><a class="dcard reveal" href="#" data-item data-cat="gala desert" data-inquiry=""><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Agafay · 40 min from Marrakech</span><h3>Private dinner in the desert</h3><p class="more">Long tables, lanterns and a stone desert to yourselves.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="gala culture" data-inquiry=""><img src="https://images.pexels.com/photos/27945049/pexels-photo-27945049.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Palace courtyard with fountain and arches" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Fès</span><h3>A palace to yourselves</h3><p class="more">Exclusive buyout of a restored palace for a celebration.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="desert" data-inquiry=""><img src="https://images.pexels.com/photos/10434667/pexels-photo-10434667.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1800" alt="Private desert camp in the Sahara" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Merzouga · Sahara</span><h3>A camp under the stars</h3><p class="more">Camel at sunset, then a private camp with a proper bath.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="culture adventure" data-inquiry=""><img src="https://images.pexels.com/photos/29976840/pexels-photo-29976840.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Kasbah in the Atlas foothills" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">High Atlas</span><h3>A Berber village day</h3><p class="more">Valley walk and lunch with a local family.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="food" data-inquiry=""><img src="https://images.pexels.com/photos/31070248/pexels-photo-31070248.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Blue boats in the harbour at Essaouira" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Essaouira</span><h3>Market to tagine</h3><p class="more">A coastal cooking class with a local cook, by the harbour.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="culture" data-inquiry=""><img src="https://images.pexels.com/photos/22711555/pexels-photo-22711555.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Craft stalls in the Fès medina" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Marrakech medina</span><h3>The after-hours souk</h3><p class="more">The medina after the crowds leave, with an artisan guide.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="food culture" data-inquiry=""><img src="https://images.pexels.com/photos/7391720/pexels-photo-7391720.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Riad lounge with carved plaster and lantern light" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Fès</span><h3>Cooking with a family</h3><p class="more">A home kitchen in the medina, and lunch at the family table.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="adventure" data-inquiry=""><img src="https://images.pexels.com/photos/37684084/pexels-photo-37684084.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1800" alt="The earthen kasbah of Aït Benhaddou" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Agafay or Atlas</span><h3>Quad tracks & mint tea</h3><p class="more">Desert tracks for teams, with a mint-tea finish line.</p></div></a><a class="dcard reveal" href="#" data-item data-cat="adventure desert" data-inquiry=""><img src="https://images.pexels.com/photos/30320422/pexels-photo-30320422.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1800" alt="Camels crossing the dunes" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var(--gold-light)">Marrakech</span><h3>Balloon at sunrise</h3><p class="more">Over the palm groves with the Atlas on the horizon.</p></div></a></div><p class="empty" data-empty hidden>Nothing in this category yet.</p>
</div></section>
<section class="final grain">
  <div class="media"><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" loading="lazy" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true" style="left:50%;top:10%"></span>
  <div class="container narrow reveal">
    <div class="star-rule" aria-hidden="true"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg></div>
    <span class="label">Add it to your journey</span>
    <h2 class="h2">Seen something you like? <span class="it block">We will build it in.</span></h2>
    <p class="lead">Mention the experience in your note and your producer will weave it into the route.</p>
    <div class="ctas"><button type="button" class="btn btn-gold " data-inquiry=""><span>Design your journey</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button><a class="btn btn-ghost" href="mailto:info@wearemoroccodmc.com">info@wearemoroccodmc.com</a></div>
  </div>
</section>
@endsection
