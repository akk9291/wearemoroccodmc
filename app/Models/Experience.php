<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Experience extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = ['title', 'slug', 'category', 'location', 'destination_id', 'image', 'image_alt', 'description',
        'status', 'sort_order'];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
