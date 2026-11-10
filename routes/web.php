<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Single pages
Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'page')->defaults('view', 'about')->name('about');
    Route::get('/why-us', 'page')->defaults('view', 'why-us')->name('why-us');
    Route::get('/experiences', 'page')->defaults('view', 'experiences')->name('experiences');
    Route::get('/faq', 'page')->defaults('view', 'faq')->name('faq');
    Route::get('/quote', 'page')->defaults('view', 'quote')->name('quote');
    Route::get('/contact', 'page')->defaults('view', 'contact')->name('contact');
    Route::get('/thank-you', 'page')->defaults('view', 'thank-you')->name('thank-you');
    Route::get('/sustainability', 'page')->defaults('view', 'sustainability')->name('sustainability');
    Route::get('/terms', 'page')->defaults('view', 'legal.terms')->name('terms');
    Route::get('/privacy', 'page')->defaults('view', 'legal.privacy')->name('privacy');
    Route::get('/cookies', 'page')->defaults('view', 'legal.cookies')->name('cookies');

    // Grouped sections: index + show/{slug}
    foreach (['services', 'destinations', 'itineraries', 'case-studies', 'journal'] as $section) {
        Route::get("/{$section}", 'page')->defaults('view', "{$section}.index")->name("{$section}.index");
        Route::get("/{$section}/{slug}", 'show')->defaults('section', $section)
            ->where('slug', '[a-z0-9-]+')->name("{$section}.show");
    }
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
