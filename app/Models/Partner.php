<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = ['name', 'logo', 'url', 'status', 'sort_order'];
}
