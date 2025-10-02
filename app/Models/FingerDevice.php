<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerDevice extends Model
{
    
    protected $fillable = ['name', 'customer_id', 'ip', 'port'];

    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
