<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamblingDepositLog extends Model
{
    protected $fillable = [
        'gambling_deposit_id',
        'changed_by',
        'field_changed',
        'old_value',
        'new_value',
    ];
}
