<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://images.pexels.com">
<meta name="csrf-token" content="{{ csrf_token() }}">
@hasSection('meta')
@yield('meta')
@else
@include('partials.seo')
@endif
<link rel="icon" href="{{ asset('assets/favicon.svg') }}" type="image/svg+xml">
{{-- Web fonts load without blocking the first paint (display=swap already shows the fallback font first). --}}
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Outfit:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Outfit:wght@300;400;500;600;700&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Outfit:wght@300;400;500;600;700&display=swap"></noscript>
<link rel="stylesheet" href="{{ \App\Support\Content::asset('assets/arena.css') }}">
<link rel="stylesheet" href="{{ \App\Support\Content::asset('assets/brand.css') }}">
@stack('head')
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
@include('partials.header')
<main id="main">
@yield('content')
</main>
@include('partials.footer')
@include('partials.inquiry')
@include('partials.fab')
@stack('scripts')
<script src="{{ \App\Support\Content::asset('assets/arena.js') }}" defer></script>
<script src="{{ \App\Support\Content::asset('assets/forms.js') }}" defer></script>
</body>
</html>
