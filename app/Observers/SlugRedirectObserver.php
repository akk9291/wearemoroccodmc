<?php

namespace App\Observers;

use App\Models;
use App\Models\Redirect;
use Illuminate\Database\Eloquent\Model;

/** When a detail page changes its slug, its old URL gets a 301 to the new one. */
class SlugRedirectObserver
{
    /** Model => detail route name */
    public const ROUTES = [
        Models\Service::class => 'services.show',
        Models\Destination::class => 'destinations.show',
        Models\Itinerary::class => 'itineraries.show',
        Models\CaseStudy::class => 'case-studies.show',
        Models\JournalPost::class => 'journal.show',
    ];

    public function updated(Model $model): void
    {
        $route = self::ROUTES[$model::class] ?? null;
        $old = $model->getOriginal('slug');
        if (! $route || ! $model->wasChanged('slug') || ! $old) {
            return;
        }
        if ($model instanceof Models\Destination && ! $model->has_page) {
            return; // no page of its own, nothing to redirect
        }

        Redirect::point(route($route, $old, false), route($route, $model->slug, false), 'slug');
    }
}
