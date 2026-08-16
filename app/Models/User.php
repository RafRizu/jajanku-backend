<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function shop(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function driverOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function isSeller(): bool
    {
        return $this->hasRole('seller');
    }

    public function isBuyer(): bool
    {
        return $this->hasRole('buyer');
    }

    public function isDriver(): bool
    {
        return $this->hasRole('driver');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
