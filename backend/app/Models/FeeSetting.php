<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeSetting extends Model
{
    protected $fillable = ['name', 'type', 'fixed_fee', 'percent_fee', 'is_active'];
}
