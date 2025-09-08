<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['member_id', 'amount', 'method', 'paid_on', 'valid_until'];
}
