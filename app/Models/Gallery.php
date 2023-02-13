<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name',
        'style', 'item_per_row'
    ];

    public function images() {
        return $this->hasMany(\App\Models\GalleryImage::class, 'gallery_id');
    }
    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
