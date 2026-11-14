<?php

use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Single pages: content comes from the pages table (slug = route name).
$pages = ['home' => '/', 'about' => '/about', 'why-us' => '/why-us', 'experiences' => '/experiences', 'faq' => '/faq',
    'quote' => '/quote', 'contact' => '/contact', 'thank-you' => '/thank-you', 'sustainability' => '/sustainability',
    'terms' => '/terms', 'privacy' => '/privacy', 'cookies' => '/cookies'];
foreach ($pages as $slug => $uri) {
    Route::get($uri, [PageController::class, 'show'])->defaults('page', $slug)->name($slug);
}

// Sections: index page (pages table) + detail by slug (published records only, 404 otherwise).
$sections = [
    'services' => [ServiceController::class, 'service'],
    'destinations' => [DestinationController::class, 'destination'],
    'itineraries' => [ItineraryController::class, 'itinerary'],
    'case-studies' => [CaseStudyController::class, 'caseStudy'],
    'journal' => [JournalController::class, 'post'],
];
foreach ($sections as $prefix => [$controller, $param]) {
    Route::get("/{$prefix}", [$controller, 'index'])->name("{$prefix}.index");
    Route::get("/{$prefix}/{{$param}}", [$controller, 'show'])->name("{$prefix}.show");
}

// Website forms (Phase 4): CSRF via the web group, validation in app/Http/Requests/Forms, rate limit "forms".
Route::middleware("throttle:forms")->prefix("forms")->name("forms.")->group(function () {
    Route::post("/inquiry", [FormController::class, "inquiry"])->name("inquiry");
    Route::post("/quote", [FormController::class, "quote"])->name("quote");
    Route::post("/contact", [FormController::class, "contact"])->name("contact");
    Route::post("/newsletter", [FormController::class, "newsletter"])->name("newsletter");
});

// 301 redirects from the old static *.html file names
$legacy = [
    'index.html' => ['home'],
    'about.html' => ['about'],
    'why-us.html' => ['why-us'],
    'experiences.html' => ['experiences'],
    'faq.html' => ['faq'],
    'quote.html' => ['quote'],
    'contact.html' => ['contact'],
    'thank-you.html' => ['thank-you'],
    'sustainability.html' => ['sustainability'],
    'terms.html' => ['terms'],
    'privacy.html' => ['privacy'],
    'cookies.html' => ['cookies'],
    'services.html' => ['services.index'],
    'services-tailor-made.html' => ['services.show', 'tailor-made'],
    'services-incentives.html' => ['services.show', 'incentives'],
    'services-events.html' => ['services.show', 'events'],
    'services-ground-handling.html' => ['services.show', 'ground-handling'],
    'destinations.html' => ['destinations.index'],
    'destination-marrakech.html' => ['destinations.show', 'marrakech'],
    'itineraries.html' => ['itineraries.index'],
    'itinerary-imperial-cities.html' => ['itineraries.show', 'imperial-cities'],
    'itinerary-magical-morocco.html' => ['itineraries.show', 'magical-morocco'],
    'itinerary-grand-tour.html' => ['itineraries.show', 'grand-tour'],
    'itinerary-desert-kasbahs.html' => ['itineraries.show', 'desert-kasbahs'],
    'itinerary-marrakech-incentive.html' => ['itineraries.show', 'marrakech-incentive'],
    'case-studies.html' => ['case-studies.index'],
    'case-study.html' => ['case-studies.show', 'agafay-gala'],
    'journal.html' => ['journal.index'],
    'journal-post.html' => ['journal.show', 'best-time-to-visit-marrakech'],
];

foreach ($legacy as $file => $target) {
    Route::get($file, function () use ($target) {
        $query = request()->getQueryString();
        $url = route(...$target);

        return redirect($query ? "{$url}?{$query}" : $url, 301);
    });
}
