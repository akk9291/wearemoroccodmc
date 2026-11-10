<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = ['quote', 'name', 'role', 'company', 'trip', 'image', 'placement', 'status', 'sort_order'];
}
