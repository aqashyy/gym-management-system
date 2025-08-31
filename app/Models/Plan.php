<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    
    protected $fillable = ['name', 'duration_months', 'customer_id', 'price'];

    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
