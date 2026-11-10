<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Header, footer and inquiry drawer read menus + site settings from the database.
        View::composer(['partials.header', 'partials.footer', 'partials.inquiry'], function ($view) {
            $view->with('site', once(fn () => Setting::allValues()));
            $view->with('menus', once(fn () => Menu::with('items')->get()->mapWithKeys(fn ($m) => [$m->name => $m->items])->all()));
        });
    }
}
