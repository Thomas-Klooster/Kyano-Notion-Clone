<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class password_reset_tokens extends Model
{
   
    protected $fillables = [
        'user_id',
        'otp',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}


