<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'parent_id', 'label', 'url', 'route', 'route_param', 'active_routes', 'sort_order'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function href(): string
    {
        if ($this->route) {
            return $this->route_param ? route($this->route, $this->route_param) : route($this->route);
        }

        return (string) $this->url;
    }

    /** Highlighted (aria-current) when the current route matches one of active_routes. */
    public function isActive(): bool
    {
        return $this->active_routes !== null && $this->active_routes !== ''
            && request()->routeIs(...explode(',', $this->active_routes));
    }
}
