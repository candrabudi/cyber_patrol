<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamblingDeposit extends Model
{
    protected $fillable = [
        'website_name',
        'website_url',
        'is_confirmed_gambling',
        'is_accessible',
        'customer_id',
        'channel_id',
        'account_number',
        'account_name',
        'report_date',
        'report_evidence',
        'link_closure_date',
        'link_closure_status',
        'account_validation_date',
        'account_validation_status',
        'report_status',
        'is_solved',
        'remarks',
        'is_non_member',
        'created_by',
    ];
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function attachments()
    {
        return $this->hasMany(GamblingDepositAttachment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(GamblingDepositLog::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function gamblingDepositAccounts()
    {
        return $this->hasMany(GamblingDepositAccount::class);
    }
    public function gamblingDepositAccount()
    {
        return $this->hasOne(GamblingDepositAccount::class);
    }
}
