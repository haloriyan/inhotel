<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'code', 'method', 'has_used', 'expiry'
    ];

    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
