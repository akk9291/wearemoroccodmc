<?php

namespace App\Models;

use App\Models\Concerns\HasHero;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudy extends Model
{
    use HasHero, Publishable, SoftDeletes;

    protected $fillable = ['client', 'title', 'slug', 'industry', 'group_size', 'location', 'year', 'challenge', 'solution',
        'results', 'gallery', 'body', 'card_kicker', 'card_title', 'card_summary', 'card_image', 'card_image_alt',
        'filter_service'];

    protected $casts = ['results' => 'array', 'gallery' => 'array'];
}
