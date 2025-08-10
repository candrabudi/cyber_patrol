<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'error_message',
        'stack_trace',
        'file',
        'line',
        'user_id',
        'ip_address',
        'occurred_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
