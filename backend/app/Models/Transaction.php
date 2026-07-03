<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'network_id', 'type', 'status', 'amount', 'fee', 'wallet_address',
        'recipient_name', 'withdraw_method', 'recipient_payload', 'txid', 'proof_path',
        'note', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:6',
            'fee' => 'decimal:6',
            'recipient_payload' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function network() { return $this->belongsTo(Network::class); }
}
