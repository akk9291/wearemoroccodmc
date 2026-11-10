@extends('layouts.app')

@section('nav', '')
@section('title')
Contact | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Contact our atelier in Marrakech: +212 653 051 512, info@wearemoroccotravel.com. Planning calls on American hours.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Contact | We Are Morocco">
<meta property="og:description" content="Contact our atelier in Marrakech: +212 653 051 512, info@wearemoroccotravel.com. Planning calls on American hours.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain short">
  <div class="media"><img src="https://images.pexels.com/photos/27945049/pexels-photo-27945049.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Palace courtyard with fountain and arches" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">Contact</li></ol><p class="eyebrow">Contact</p>
    <h1 class="h1" style="margin-top:1.25rem">Let’s plan something <span class="it block">beautiful in Morocco.</span></h1>
    <p class="lead">The fastest way to a first sketch is a short note. For anything else, our team replies within one business day.</p>
    <div class="ctas"><button type="button" class="btn btn-gold " data-inquiry=""><span>Begin a private conversation</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button></div>
  </div>
</section>
<section class="sec bg-parch"><div class="container cols c-5-7 items-start">
  <div class="reveal"><p class="eyebrow">Reach us</p><h2 class="h2 big">Our atelier <span class="it">in Marrakech.</span></h2>
    <dl class="cblock"><div><dt>Phone & WhatsApp</dt><dd><a href="tel:+212653051512">+212 653 051 512</a></dd></div><div><dt>Email</dt><dd><a href="mailto:info@wearemoroccotravel.com">info@wearemoroccotravel.com</a></dd></div><div><dt>Atelier</dt><dd>Av. Hassan II, Koutoubia Center II, 1er Étage, Bureau N°5, Marrakech</dd></div><div><dt>Hours</dt><dd>Planning calls on Eastern &amp; Pacific time, by appointment</dd></div><div><dt>Guests on a journey</dt><dd>24/7 WhatsApp line</dd></div></dl></div>
  <div class="builder reveal-right" style="padding:2rem"><span class="kicker" style="color:var(--gold-light)">General enquiry</span><h3 class="h3" style="color:var(--ivory)">Send us <span class="it">a message.</span></h3>
    <p class="small" style="margin-top:.75rem;color:rgba(239,230,214,.65)">Planning a journey? The <a href="{{ route('quote') }}" style="color:var(--gold-light)">six-question brief</a> gets you a first sketch faster.</p>
    <form class="form-grid" data-simple novalidate style="margin-top:1.75rem">
      <div class="form-grid two"><label class="lbl">Name *<input class="field" required autocomplete="name" placeholder="Your name"><span class="err">Please add your name.</span></label>
      <label class="lbl">Email *<input class="field" type="email" required autocomplete="email" placeholder="you@studio.com"><span class="err">Please add a valid email address.</span></label></div>
      <label class="lbl">Message *<textarea class="field" required rows="5" placeholder="How can we help?"></textarea><span class="err">Please write a short message.</span></label>
      <label class="check lbl" style="text-transform:none;letter-spacing:0;font-size:.875rem"><input type="checkbox" required> I agree to the <a href="{{ route('privacy') }}" style="color:var(--gold-light)">privacy policy</a>.<span class="err">Please accept the privacy policy.</span></label>
      <button class="btn btn-gold" type="submit"><span>Send message</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
      <p class="notice" data-done hidden>Thank you. Design preview only: on the live site this message goes to the atelier, who reply within one business day.</p>
    </form></div>
</div></section>
@endsection
