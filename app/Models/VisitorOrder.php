<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'visitor_id', 'product_id', 'payment_id',
        'quantity', 'total', 'is_placed', 'has_used'
    ];

    public function product() {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
    public function visitor() {
        return $this->belongsTo(\App\Models\Visitor::class, 'visitor_id');
    }
    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function payment() {
        return $this->belongsTo(\App\Models\Payment::class, 'payment_id');
    }
}
