<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = ['question', 'answer', 'group', 'status', 'sort_order'];
}
