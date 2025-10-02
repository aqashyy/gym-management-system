<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaMessageTemplate extends Model
{
    protected $fillable = ['title', 'name', 'customer_id', 'content', 'is_active'];
}
