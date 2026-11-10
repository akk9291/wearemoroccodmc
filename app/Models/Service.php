<?php

namespace App\Models;

use App\Models\Concerns\HasHero;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasHero, Publishable, SoftDeletes;

    protected $fillable = ['name', 'slug', 'eyebrow', 'intro', 'highlights', 'card_kicker', 'card_summary', 'card_image',
        'card_image_alt'];

    protected $casts = ['highlights' => 'array'];
}
