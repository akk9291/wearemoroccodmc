{{-- Page not found, in the site design. Unsaved Page model so the normal hero partial renders it. --}}
@extends('layouts.app')

@php($m = new \App\Models\Page([
    'title' => 'Page not found',
    'meta_title' => 'Page not found | '.\App\Models\Setting::get('site_name', 'We Are Morocco'),
    'meta_description' => 'This page has moved or no longer exists. Find your way back to our journeys, destinations and atelier.',
    'hero_image' => 'https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600',
    'hero_image_alt' => 'A quiet riad courtyard in Marrakech',
    'hero_eyebrow' => 'Error 404',
    'hero_title' => 'A wrong turn <span class="it block">in the medina.</span>',
    'hero_subtitle' => 'The page you were looking for has moved or no longer exists. Every lane leads somewhere: here are a few good ones.',
]))
@php($arrow = \App\Support\Content::icon('arrow'))

@section('meta')
@include('partials.seo', ['m' => $m, 'seoOverrides' => ['robots' => 'noindex, follow']])
@endsection

@section('content')
@include('partials.hero', ['m' => $m, 'extra' => '<div class="ctas"><a class="btn btn-gold" href="'.e(route('home')).'"><span>Back to the homepage</span>'.$arrow.'</a><button type="button" class="btn btn-ghost" data-inquiry=""><span>Ask the atelier</span></button></div>'])
<section class="sec bg-parch"><div class="container">
  <div class="head-row"><div class="reveal"><p class="eyebrow">Find your way</p><h2 class="h2 big">Where to <span class="it">next.</span></h2></div></div>
  <div class="grid g-3 mt-14">
    <a class="vcard lift reveal" href="{{ route('itineraries.index') }}"><span class="ico">{!! \App\Support\Content::icon('doc') !!}</span><h3>Signature journeys</h3><p>Imperial, Magical and The Grand Morocco, day by day.</p></a>
    <a class="vcard lift reveal" href="{{ route('destinations.index') }}" style="transition-delay:80ms"><span class="ico">{!! \App\Support\Content::icon('check') !!}</span><h3>Destinations</h3><p>Marrakech, Fès, the Sahara and the places in between.</p></a>
    <a class="vcard lift reveal" href="{{ route('contact') }}" style="transition-delay:160ms"><span class="ico">{!! \App\Support\Content::icon('arrow') !!}</span><h3>Contact the atelier</h3><p>Tell us what you were looking for; we answer as people.</p></a>
  </div>
</div></section>
@endsection
