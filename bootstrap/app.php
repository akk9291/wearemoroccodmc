<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirects table (admin + old .html names + slug changes), before routing so unknown paths can redirect.
        $middleware->prepend(\App\Http\Middleware\HandleRedirects::class);
        // Guest full-page cache (after the session starts, so the CSRF token can be put back in).
        $middleware->appendToGroup('web', \App\Http\Middleware\CacheGuestPages::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
