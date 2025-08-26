<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestGamblingDeposit extends Model
{

    protected $table = 'request_gambling_deposits';

    protected $fillable = [
        'website_id',
        'channel_id',
        'requested_by',
        'to_user',
        'reason',
        'status',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'to_user');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }
}
