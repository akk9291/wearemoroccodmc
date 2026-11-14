<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = ['form_type', 'name', 'email', 'intent', 'travelers', 'dates', 'note', 'source_page', 'ip', 'status',
        'admin_notes'];
}
