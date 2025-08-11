<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamblingDeposit extends Model
{
    protected $fillable = [
        // Website Information
        'website_name',
        'website_url',
        'is_confirmed_gambling',
        'is_accessible',

        // Payment Account Information
        'channel_id',
        'account_number',
        'account_name',

        // Coordination with Authority (Kominfo)
        'report_date',
        'report_evidence',
        'link_closure_date',
        'link_closure_status',

        // Account Validation Result
        'account_validation_date',
        'account_validation_status',

        // Report Workflow Status
        'report_status',

        // Additional
        'is_solved',
        'remarks',

        // Audit
        'created_by',
    ];

    public function attachments()
    {
        return $this->hasMany(GamblingDepositAttachment::class);
    }

    public function channel()
    {
        return $this->hasOne(Channel::class, 'id', 'channel_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(GamblingDepositLog::class, 'gambling_deposit_id', 'id');
    }

    public function provider()
    {
        return $this->hasOneThrough(
            Provider::class,
            Channel::class,
            'id',          // channel.id
            'id',          // provider.id
            'channel_id',  // gambling_deposits.channel_id
            'provider_id'  // channels.provider_id
        );
    }

    public function customers()
    {
        return $this->hasManyThrough(
            Customer::class,
            CustomerProvider::class,
            'provider_id', // customer_providers.provider_id
            'id',          // customers.id
            'provider_id', // providers.id
            'customer_id'  // customer_providers.customer_id
        );
    }
}
