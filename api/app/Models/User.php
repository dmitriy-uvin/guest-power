<?php

namespace App\Models;

use App\Models\Constants\UserRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'skype',
        'website',
        'role',
        'email_verified_at',
        'blocked'
    ];

    protected $attributes = [
        'role' => UserRoles::USER,
        'blocked' => false,
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function filters()
    {
        return $this->belongsToMany(Filter::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
