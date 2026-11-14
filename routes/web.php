<?php

use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
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

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Website forms (Phase 4): CSRF via the web group, validation in app/Http/Requests/Forms, rate limit "forms".
Route::middleware("throttle:forms")->prefix("forms")->name("forms.")->group(function () {
    Route::post("/inquiry", [FormController::class, "inquiry"])->name("inquiry");
    Route::post("/quote", [FormController::class, "quote"])->name("quote");
    Route::post("/contact", [FormController::class, "contact"])->name("contact");
    Route::post("/newsletter", [FormController::class, "newsletter"])->name("newsletter");
});

// 301s from the old static *.html names live in the redirects table (Database\Seeders\RedirectSeeder),
// handled by App\Http\Middleware\HandleRedirects together with the redirects added in the admin.
