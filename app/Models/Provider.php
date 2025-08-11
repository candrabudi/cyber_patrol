<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'provider_code', 'provider_type'];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_providers', 'provider_id', 'customer_id')
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'customer_providers', 'provider_id', 'customer_id')
            ->withTimestamps();
    }

    public function channels()
    {
        return $this->hasMany(Channel::class, 'provider_id', 'id');
    }
}
