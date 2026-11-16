<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('We Are Morocco DMC')
            // gold wordmark on a small dark plate so it reads on the light admin theme too
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('<span style="display:inline-flex;align-items:center;background:#0e0c0a;padding:.45rem .8rem;border-radius:.6rem"><img src="'.e(asset('assets/img/logo/wearemorocco-wordmark-gold.png')).'" alt="We Are Morocco DMC" style="height:1.35rem;width:auto;display:block"></span>'))
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('assets/favicon.svg'))
            ->colors([
                // --gold / --terracotta in public/assets/arena.css
                'primary' => Color::hex('#c4a06a'),
                'danger' => Color::hex('#b85c38'),
            ])
            ->font('Outfit')
            ->databaseNotifications()
            ->navigationGroups(['Content', 'Leads', 'Site', 'System'])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
