<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    protected $fillable = ['user_id', 'full_name', 'phone', 'id_image_path', 'selfie_image_path', 'status', 'admin_note', 'reviewed_at'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
}
