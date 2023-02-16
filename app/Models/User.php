<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name','username','email','password',
        'photo', 'cover', 'accent_color', 'font_family', 'is_active',
        'token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function socials() {
        return $this->hasMany(\App\Models\Social::class, 'user_id');
    }
    public function products() {
        return $this->hasMany(\App\Models\Product::class, 'user_id');
    }
    public function premium() {
        return $this->hasOne(\App\Models\UserPremium::class, 'user_id')->latest();
    }
    public function banks() {
        return $this->hasMany(\App\Models\Bank::class, 'user_id');
    }
    public function withdraws() {
        return $this->hasMany(\App\Models\UserWithdraw::class, 'user_id');
    }
    public function banners() {
        return $this->hasMany(\App\Models\Banner::class, 'user_id');
    }
}
