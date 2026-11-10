@extends('layouts.app')

@section('nav', '')
@section('title')
Thank you | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Your note is with our atelier in Marrakech.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Thank you | We Are Morocco">
<meta property="og:description" content="Your note is with our atelier in Marrakech.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain">
  <div class="media"><img src="https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Golden Sahara dunes at sunset in Merzouga" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <p class="eyebrow">Request received</p>
    <h1 class="h1" style="margin-top:1.25rem">Thank you. <span class="it block">Your note is with Marrakech.</span></h1>
    <p class="lead">A producer will reply within one business day — usually sooner. Reference: <span class="ph" title="Placeholder: reference">[WAM-2026-0123]</span>.</p>
    <div class="ctas"><a class="btn btn-ghost" href="{{ route('home') }}">Back to the homepage</a></div>
  </div>
</section>
<section class="sec bg-parch"><div class="container">
  <div class="draft reveal" id="ty-brief" hidden style="max-width:36rem;margin-bottom:3.5rem;background:var(--ink)"><h4>Your brief</h4><pre style="margin:.75rem 0 0;white-space:pre-wrap;font:400 .875rem/1.6 var(--font-sans);color:rgba(239,230,214,.8)"></pre></div>
  <div class="head-row"><div class="reveal"><p class="eyebrow">While you wait</p><h2 class="h2 big">Next <span class="it">steps.</span></h2></div></div>
  <div class="grid g-3 mt-14"><a class="vcard lift reveal" href="{{ route('itineraries.index') }}" style="transition-delay:0ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg></span><h3>Browse the signature journeys</h3><p>Imperial, Magical and The Grand Morocco, day by day.</p></a><a class="vcard lift reveal" href="{{ route('experiences') }}" style="transition-delay:80ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5z"/></svg></span><h3>Explore experiences</h3><p>Private dinners, desert camps and more.</p></a><a class="vcard lift reveal" href="tel:+212653051512" style="transition-delay:160ms"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"/></svg></span><h3>WhatsApp the atelier</h3><p>+212 653 051 512 — we answer on your clock.</p></a></div>
</div></section>
@endsection
