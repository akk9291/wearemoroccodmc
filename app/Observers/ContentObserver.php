<?php

namespace App\Observers;

use App\Support\ContentCache;
use Illuminate\Database\Eloquent\Model;

/** Any change to site content makes the cached sitemap, menus, listings and guest pages stale. */
class ContentObserver
{
    public function saved(Model $model): void
    {
        ContentCache::flush();
    }

    public function deleted(Model $model): void
    {
        ContentCache::flush();
    }

    public function restored(Model $model): void
    {
        ContentCache::flush();
    }

    public function forceDeleted(Model $model): void
    {
        ContentCache::flush();
    }
}
