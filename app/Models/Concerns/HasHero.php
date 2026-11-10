<?php

namespace App\Models\Concerns;

/** Casts and fillable columns for the shared hero + SEO columns. */
trait HasHero
{
    public const HERO_FIELDS = ['hero_variant', 'hero_image', 'hero_image_alt', 'hero_crumbs', 'hero_eyebrow',
        'hero_title', 'hero_subtitle', 'hero_extra'];

    public const SEO_FIELDS = ['meta_title', 'meta_description', 'og_image'];

    public function initializeHasHero(): void
    {
        $this->mergeFillable([...self::HERO_FIELDS, ...self::SEO_FIELDS, 'sections', 'status', 'sort_order']);
        $this->mergeCasts(['hero_crumbs' => 'array', 'sections' => 'array']);
    }
}
