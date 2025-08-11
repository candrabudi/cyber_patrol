<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamblingDepositAttachment extends Model
{
    protected $fillable = [
        'gambling_deposit_id',
        'attachment_type',
        'file_path',
    ];
}
