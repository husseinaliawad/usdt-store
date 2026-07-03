<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'network_id', 'address', 'balance', 'is_primary'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'balance' => 'decimal:6'];
    }

    public function network() { return $this->belongsTo(Network::class); }
    public function user() { return $this->belongsTo(User::class); }
}
