<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'customer_id',
        'channel_code',
        'channel_type',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
