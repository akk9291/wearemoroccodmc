{{-- The one SEO head for every page (App\Support\Seo): pass $m (the record) or nothing; $seoOverrides optional. --}}
@php($seo = \App\Support\Seo::for($m ?? null, $seoOverrides ?? []))
<title>{{ $seo->title }}</title>
<meta name="description" content="{{ $seo->description }}">
<meta name="robots" content="{{ $seo->robots }}">
<link rel="canonical" href="{{ $seo->canonical }}">
<meta name="theme-color" content="#0e0c0a">
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:site_name" content="{{ \App\Models\Setting::get('site_name', 'We Are Morocco') }}">
<meta property="og:locale" content="en_US">
<meta property="og:url" content="{{ $seo->canonical }}">
<meta property="og:title" content="{{ $seo->title }}">
<meta property="og:description" content="{{ $seo->description }}">
@if($seo->image)<meta property="og:image" content="{{ $seo->image }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->title }}">
<meta name="twitter:description" content="{{ $seo->description }}">
@if($seo->image)<meta name="twitter:image" content="{{ $seo->image }}">
@endif
<script type="application/ld+json">{!! $seo->jsonLd() !!}</script>
