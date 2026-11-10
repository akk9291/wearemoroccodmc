<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteRequest extends Model
{
    use SoftDeletes;

    public const STATUSES = ['new', 'contacted', 'quoted', 'won', 'lost'];

    protected $fillable = ['name', 'email', 'intent', 'party', 'travelers', 'dates', 'length', 'pace', 'mood', 'stays', 'prio',
        'lead', 'note', 'status', 'admin_notes', 'source_page', 'ip'];
}
