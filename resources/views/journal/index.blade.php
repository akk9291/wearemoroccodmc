@extends('layouts.app')

@section('nav', 'journal')
@section('title')
Journal | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Notes from the ground: seasons, houses, logistics and the places we love in Morocco.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Journal | We Are Morocco">
<meta property="og:description" content="Notes from the ground: seasons, houses, logistics and the places we love in Morocco.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain short">
  <div class="media"><img src="https://images.pexels.com/photos/31070248/pexels-photo-31070248.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Blue boats in the harbour at Essaouira" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">Journal</li></ol><p class="eyebrow">Journal</p>
    <h1 class="h1" style="margin-top:1.25rem">Notes <span class="it">from the ground.</span></h1>
    <p class="lead">Guides for travelers and planners: seasons, houses, logistics and the places we love.</p>
    
  </div>
</section>
<section class="sec bg-parch"><div class="container">
  <div class="reveal"><a href="{{ route('journal.show', 'best-time-to-visit-marrakech') }}" class="scard lift reveal" style="transition-delay:0ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/15260622/pexels-photo-15260622.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Marrakech palace interior" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Featured · Planning</span><h3>Best time to visit — or host — in Marrakech</h3><p>Month by month: weather, prices, Ramadan and the city’s busiest weeks.</p><span class="link">Read the guide <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a></div>
  <div class="filters mt-14"><div class="row"><span class="kicker">Topic</span><div class="chips" data-filter-group="topic" data-target="#post-grid"><button type="button" class="chip" data-value="all" aria-pressed="true">All</button><button type="button" class="chip" data-value="planning" aria-pressed="false">Planning</button><button type="button" class="chip" data-value="incentive" aria-pressed="false">Incentives</button><button type="button" class="chip" data-value="venues" aria-pressed="false">Venues</button><button type="button" class="chip" data-value="culture" aria-pressed="false">Culture</button></div></div></div>
  <div class="grid g-2" id="post-grid" style="gap:1.5rem"><a href="{{ route('journal.show', 'best-time-to-visit-marrakech') }}" class="scard lift reveal" data-item data-topic="planning" style="transition-delay:0ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/15260622/pexels-photo-15260622.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Marrakech palace interior" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Planning · 6 min</span><h3>Best time to visit — or host — in Marrakech</h3><p>Month by month: weather, prices, Ramadan and the city’s busiest weeks.</p><span class="link">Read <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a><a href="{{ route('journal.show', 'best-time-to-visit-marrakech') }}" class="scard lift reveal" data-item data-topic="incentive" style="transition-delay:80ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/998631/pexels-photo-998631.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1800" alt="Sahara dunes" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Incentives · 8 min</span><h3>Planning a Sahara incentive</h3><p>How to get a group to Merzouga comfortably, and what to do once you are there.</p><span class="link">Read <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a><a href="{{ route('journal.show', 'best-time-to-visit-marrakech') }}" class="scard lift reveal" data-item data-topic="venues" style="transition-delay:0ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/7391720/pexels-photo-7391720.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Riad lounge with carved plaster and lantern light" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Venues · 5 min</span><h3>Five riads that take a private buyout</h3><p>Small houses that close their doors for your group.</p><span class="link">Read <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a><a href="{{ route('journal.show', 'best-time-to-visit-marrakech') }}" class="scard lift reveal" data-item data-topic="culture" style="transition-delay:80ms"><div class="pic img-zoom"><img src="https://images.pexels.com/photos/22711555/pexels-photo-22711555.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Craft stalls in the Fès medina" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">Culture · 7 min</span><h3>A first-timer’s guide to the Fès medina</h3><p>How a guided day in the medina works — and how to leave room for wandering.</p><span class="link">Read <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></div></a></div>
  <p class="empty" data-empty hidden>No posts in this topic yet.</p>
</div></section>
<section class="sec bg-ink center"><span class="orb orb-soft" aria-hidden="true"></span><div class="container narrow-2 reveal" style="margin-inline:auto"><p class="eyebrow">Newsletter</p><h2 class="h2 big">New houses and ideas, <span class="it">once a month.</span></h2>
  <form class="news" data-simple novalidate style="margin:2rem auto 0"><label class="lbl" for="j-news">Your email</label><div class="row"><input class="field" id="j-news" type="email" required placeholder="you@studio.com"><button class="btn btn-gold" type="submit">Subscribe</button></div><p class="ok" data-done hidden>Thank you. Design preview only.</p></form></div></section>
@endsection
