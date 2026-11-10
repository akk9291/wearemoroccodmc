<?php

namespace App\Models;

use App\Models\Concerns\HasHero;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalPost extends Model
{
    use HasHero, Publishable, SoftDeletes;

    protected $fillable = ['category_id', 'title', 'slug', 'excerpt', 'body', 'cover_image', 'cover_image_alt', 'author',
        'reading_minutes', 'published_at', 'published_label', 'is_featured'];

    protected $casts = ['published_at' => 'datetime', 'is_featured' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(JournalCategory::class, 'category_id');
    }
}
