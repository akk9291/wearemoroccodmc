<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/** status draft|published, ordering, and slug route binding that only resolves published rows (drafts too for signed-in admins). */
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

    /** slug when the model has one (pages, itineraries...), id otherwise (FAQs, testimonials, partners). */
    public function getRouteKeyName(): string
    {
        return in_array('slug', $this->getFillable(), true) ? 'slug' : $this->getKeyName();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        // Team members signed in to the admin may preview drafts (rendered with noindex).
        $query = \App\Support\Seo::canPreviewDrafts() ? $this->newQuery() : $this->published();

        return $query->where($field ?? $this->getRouteKeyName(), $value)->first();
    }
}
