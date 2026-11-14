<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Cache for anything built from site content (sitemap, menus, listings, guest pages).
 * Every key carries a version number; saving or deleting any content model bumps it (App\Observers\ContentObserver),
 * so all of it goes stale at once on any cache store, without cache tags.
 */
class ContentCache
{
    public const VERSION_KEY = 'content-cache-version';

    public static function version(): int
    {
        return (int) Cache::rememberForever(self::VERSION_KEY, fn () => 1);
    }

    public static function key(string $key): string
    {
        return 'content.'.static::version().'.'.$key;
    }

    public static function remember(string $key, Closure $callback, ?int $seconds = null): mixed
    {
        return $seconds === null
            ? Cache::rememberForever(static::key($key), $callback)
            : Cache::remember(static::key($key), $seconds, $callback);
    }

    public static function flush(): void
    {
        $next = static::version() + 1;
        Cache::forever(self::VERSION_KEY, $next);
    }
}
