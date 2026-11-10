<?php

namespace App\Models;

use App\Models\Concerns\HasHero;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use HasHero, Publishable, SoftDeletes;

    protected $fillable = ['name', 'slug', 'region', 'best_season', 'best_for', 'summary', 'intro', 'tagline', 'featured_name',
        'featured_image', 'featured_image_alt', 'featured_order', 'card_image', 'card_image_alt', 'has_page', 'is_featured',
        'highlights', 'gallery'];

    protected $casts = ['highlights' => 'array', 'gallery' => 'array', 'has_page' => 'boolean', 'is_featured' => 'boolean'];

    public function itineraries(): BelongsToMany
    {
        return $this->belongsToMany(Itinerary::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    /** Only destinations with their own page resolve at /destinations/{slug}. */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->published()->where('has_page', true)->where($field ?? 'slug', $value)->first();
    }
}
