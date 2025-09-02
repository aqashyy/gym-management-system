<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    
    protected $fillable = ['customer_id', 'plan_id', 'name', 'dob', 'phone', 'blood_group', 'weight', 'height', 'joining_date', 'photo','fingerprint_id', 'is_staff', 'plan_expiry'];

    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
