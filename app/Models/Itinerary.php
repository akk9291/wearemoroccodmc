<?php

namespace App\Models;

use App\Models\Concerns\HasHero;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itinerary extends Model
{
    use HasHero, Publishable, SoftDeletes;

    protected $fillable = ['title', 'slug', 'number', 'tagline', 'days', 'nights', 'duration_label', 'price_from', 'currency',
        'theme', 'route', 'card_route', 'route_stops', 'summary', 'character', 'highlights', 'includes', 'excludes', 'gallery', 'route_map', 'route_map_alt',
        'is_signature', 'inquiry_key', 'filter_type', 'filter_dest', 'filter_len', 'card_image', 'card_image_alt', 'final_cta'];

    protected $casts = ['route_stops' => 'array', 'highlights' => 'array', 'includes' => 'array', 'excludes' => 'array',
        'gallery' => 'array', 'final_cta' => 'array', 'is_signature' => 'boolean', 'price_from' => 'decimal:2'];

    public function itineraryDays(): HasMany
    {
        return $this->hasMany(ItineraryDay::class)->orderBy('day_no');
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class);
    }

    public function scopeSignature(Builder $query): Builder
    {
        return $query->where('is_signature', true);
    }
}
