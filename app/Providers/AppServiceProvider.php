<?php

namespace App\Providers;

use App\Models;
use App\Policies\AdminOnlyPolicy;
use App\Policies\ContentPolicy;
use App\Policies\LeadPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Admin panel access by role (see app/Policies).
        $policies = [
            ContentPolicy::class => [
                Models\Page::class, Models\Service::class, Models\Destination::class, Models\Itinerary::class,
                Models\ItineraryDay::class, Models\Experience::class, Models\CaseStudy::class, Models\JournalPost::class,
                Models\JournalCategory::class, Models\Faq::class, Models\Testimonial::class, Models\Partner::class,
                Models\Menu::class, Models\MenuItem::class,
            ],
            LeadPolicy::class => [Models\QuoteRequest::class, Models\ContactMessage::class, Models\NewsletterSubscriber::class],
            AdminOnlyPolicy::class => [Models\User::class, Role::class, Models\Setting::class],
        ];
        foreach ($policies as $policy => $models) {
            foreach ($models as $model) {
                Gate::policy($model, $policy);
            }
        }

        // Header, footer and inquiry drawer read menus + site settings from the database.
        View::composer(['partials.header', 'partials.footer', 'partials.inquiry'], function ($view) {
            $view->with('site', Models\Setting::allValues());
            $view->with('menus', Models\Menu::with('items')->get()->mapWithKeys(fn ($m) => [$m->name => $m->items])->all());
        });
    }
}
