<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'full_name', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'customer_providers', 'customer_id', 'provider_id')
            ->withTimestamps();
    }
}
