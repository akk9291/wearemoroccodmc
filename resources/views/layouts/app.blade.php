<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{!! trim($__env->yieldContent('title')) !!}</title>
@yield('meta')
<link rel="icon" href="{{ asset('assets/favicon.svg') }}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://images.pexels.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Outfit:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="{{ asset('assets/arena.css') }}">
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
<script src="{{ asset('assets/arena.js') }}"></script>
<script src="{{ asset('assets/forms.js') }}"></script>
</body>
</html>
