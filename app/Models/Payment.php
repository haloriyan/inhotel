<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'visitor_id', 'invoice_number',
        'external_id', 'total', 'grand_total', 'payment_link', 'status', 'has_withdrawn'
    ];

    public function items() {
        return $this->hasMany(\App\Models\VisitorOrder::class, 'payment_id');
    }
}
