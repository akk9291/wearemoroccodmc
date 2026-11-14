<?php

namespace Database\Seeders;

use App\Models\Redirect;
use Illuminate\Database\Seeder;

/** 301s from the old static site's *.html file names (formerly closures in routes/web.php). Safe to re-run. */
class RedirectSeeder extends Seeder
{
    public const LEGACY = [
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

    public function run(): void
    {
        foreach (self::LEGACY as $file => $target) {
            Redirect::updateOrCreate(['from_path' => '/'.$file], [
                'to_path' => route($target[0], $target[1] ?? [], false),
                'status_code' => 301,
                'source' => 'legacy',
            ]);
        }
    }
}
