@extends('layouts.app')

@section('nav', '')
@section('title')
Cookie policy | We Are Morocco
@endsection

@section('meta')
<meta name="description" content="Cookie policy for We Are Morocco Travel.">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:title" content="Cookie policy | We Are Morocco">
<meta property="og:description" content="Cookie policy for We Are Morocco Travel.">
<meta property="og:image" content="https://images.pexels.com/photos/38891222/pexels-photo-38891222.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=2400">
@endsection

@section('content')

<section class="phero grain short">
  <div class="media"><img src="https://images.pexels.com/photos/27945049/pexels-photo-27945049.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="Palace courtyard with fountain and arches" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>
  <span class="orb orb-gold" aria-hidden="true"></span>
  <div class="container hero-content rise">
    <ol class="crumbs"><li><a href="{{ route('home') }}">Home</a></li><li aria-current="page">Cookie policy</li></ol><p class="eyebrow">Legal</p>
    <h1 class="h1" style="margin-top:1.25rem">Cookie policy</h1>
    <p class="lead">Last updated <span class="ph" title="Placeholder: date">[date]</span>.</p>
    
  </div>
</section>
<section class="sec bg-ivory"><div class="container cols c-8-4 items-start">
  <article class="prose"><p class="note">Legal text to be supplied and approved by the company’s legal adviser. The headings below show the expected structure.</p>
  <h2 id="s0">What cookies are</h2><p>[Text to be supplied]</p><h2 id="s1">Cookies we use</h2><p>[Text to be supplied]</p><h2 id="s2">Analytics</h2><p>[Text to be supplied]</p><h2 id="s3">Managing cookies</h2><p>[Text to be supplied]</p></article>
  <aside class="sticky"><div class="box"><span class="kicker">Sections</span><nav class="toc"><a href="#s0">What cookies are</a><a href="#s1">Cookies we use</a><a href="#s2">Analytics</a><a href="#s3">Managing cookies</a></nav></div></aside>
</div></section>
@endsection
