<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'channel_code',
        'channel_type',
        'provider_id',
    ];

    public function provider()
    {
        return $this->hasOne(Provider::class, 'id', 'provider_id');
    }
}
