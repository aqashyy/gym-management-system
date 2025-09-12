<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['member_id', 'amount', 'recieved_amount', 'method', 'paid_on', 'valid_until'];


    public function Member()
    {
        return $this->belongsTo(Member::class);
    }
}
