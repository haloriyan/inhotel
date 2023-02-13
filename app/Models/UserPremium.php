<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPremium extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'external_id', 'payment_link', 'amount', 'status', 'active_until', 'month_quantity'
    ];
}
