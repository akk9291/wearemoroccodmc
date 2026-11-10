@extends('layouts.app')

@section('nav', '')
@section('title')
Terms and conditions | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Terms and conditions for We Are Morocco Travel.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Terms and conditions | We Are Morocco">
<meta property="og:description" content="Terms and conditions for We Are Morocco Travel.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain short">
  <div class="media"><img src="https://images.pexels.com/photos/27945049/pexels-photo-27945049.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Palace courtyard with fountain and arches" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">Terms and conditions</li></ol><p class="eyebrow">Legal</p>
    <h1 class="h1" style="margin-top:1.25rem">Terms and conditions</h1>
    <p class="lead">Last updated <span class="ph" title="Placeholder: date">[date]</span>.</p>
    
  </div>
</section>
<section class="sec bg-ivory"><div class="container cols c-8-4 items-start">
  <article class="prose"><p class="note">Legal text to be supplied and approved by the company’s legal adviser. The headings below show the expected structure.</p>
  <h2 id="s0">Bookings and proposals</h2><p>[Text to be supplied]</p><h2 id="s1">Payments</h2><p>[Text to be supplied]</p><h2 id="s2">Changes and cancellations</h2><p>[Text to be supplied]</p><h2 id="s3">Our responsibilities</h2><p>[Text to be supplied]</p><h2 id="s4">Your responsibilities</h2><p>[Text to be supplied]</p><h2 id="s5">Insurance</h2><p>[Text to be supplied]</p><h2 id="s6">Governing law</h2><p>[Text to be supplied]</p></article>
  <aside class="sticky"><div class="box"><span class="kicker">Sections</span><nav class="toc"><a href="#s0">Bookings and proposals</a><a href="#s1">Payments</a><a href="#s2">Changes and cancellations</a><a href="#s3">Our responsibilities</a><a href="#s4">Your responsibilities</a><a href="#s5">Insurance</a><a href="#s6">Governing law</a></nav></div></aside>
</div></section>
@endsection
