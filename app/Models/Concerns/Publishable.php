<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/** status draft|published, ordering, and slug route binding that only resolves published rows. */
trait Publishable
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_PUBLISHED);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy($this->qualifyColumn('sort_order'))->orderBy($this->getQualifiedKeyName());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->published()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }
}
