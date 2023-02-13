<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category',
        'name', 'description', 'price',
        'visibility', 'quantity_per_day'
    ];

    public function images() {
        return $this->hasMany(\App\Models\ProductImage::class, 'product_id')
                ->orderBy('priority', 'DESC')->orderBy('created_at', 'DESC');
    }
}
