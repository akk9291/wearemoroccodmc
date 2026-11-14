<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteRequest extends Model
{
    use SoftDeletes;

    public const FORM_TYPES = ['quote' => 'Quote builder', 'inquiry' => 'Inquiry drawer'];

    public const STATUSES = ['new', 'contacted', 'quoted', 'won', 'lost'];

    protected $fillable = ['form_type', 'name', 'email', 'intent', 'party', 'travelers', 'dates', 'length', 'pace', 'mood', 'stays', 'prio',
        'lead', 'note', 'brief', 'status', 'admin_notes', 'source_page', 'ip'];
}
