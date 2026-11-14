{{-- Server error. Deliberately standalone: no database, settings or menus, so it renders even when those are the problem. --}}
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Something went wrong | We Are Morocco</title>
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#0e0c0a">
<link rel="icon" href="{{ asset('assets/favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Outfit:wght@300;400;500&display=swap">
<link rel="stylesheet" href="{{ asset('assets/arena.css') }}">
</head>
<body>
<main id="main">
<section class="sec bg-ink center" style="min-height:100vh;display:grid;place-items:center">
  <span class="orb orb-soft" aria-hidden="true"></span>
  <div class="container narrow-2" style="margin-inline:auto">
    <a class="brand" href="{{ url('/') }}" aria-label="We Are Morocco, home" style="justify-content:center;margin-bottom:2.5rem"><svg viewBox="0 0 32 32" aria-hidden="true" class="star"><path fill="currentColor" d="M16 2.5 18.4 12.2 28.5 14.5 18.4 16.8 16 26.5 13.6 16.8 3.5 14.5 13.6 12.2Z"/></svg><span class="word">weare<i>morocco</i></span></a>
    <p class="eyebrow">Error 500</p>
    <h1 class="h2 big" style="margin-top:1rem">Something went wrong <span class="it">on our side.</span></h1>
    <p class="lead" style="margin-top:1.25rem;color:rgba(239,230,214,.78)">We have been notified. Please try again in a moment, or write to us at <a href="mailto:info@wearemoroccotravel.com" style="color:var(--gold-light)">info@wearemoroccotravel.com</a>.</p>
    <div class="ctas" style="justify-content:center;margin-top:2rem"><a class="btn btn-gold" href="{{ url('/') }}"><span>Back to the homepage</span></a></div>
  </div>
</section>
</main>
</body>
</html>
