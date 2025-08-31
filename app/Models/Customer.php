<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Members()
    {
        return $this->hasMany(Member::class);
    }

    public function Plans()
    {
        return $this->hasMany(Plan::class);
    }
}
