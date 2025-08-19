<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = [
        'website_name',
        'website_url',
        'website_proofs',
        'is_confirmed_gambling',
        'is_accessible',
        'created_by',
    ];
}
