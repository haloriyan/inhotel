<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWithdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bank_id', 'withdraw_id', 'amount', 'status', 'eta'
    ];

    public function bank() {
        return $this->belongsTo(\App\Models\Bank::class, 'bank_id');
    }
}
