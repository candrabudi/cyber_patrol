<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function customerRequest()
    {
        return $this->hasOne(RequestGamblingDeposit::class, 'website_id', 'id')
            ->where('requested_by', Auth::user()->id);
    }
}
