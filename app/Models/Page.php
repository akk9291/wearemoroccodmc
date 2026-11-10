<?php

namespace App\Models;

use App\Models\Concerns\HasHero;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasHero, Publishable, SoftDeletes;

    protected $fillable = ['title', 'slug', 'template'];
}
