<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'image',
        'status',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // Relationships
    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activeProducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class)->where('is_available', true);
    }

    // Scope for active shops
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Calculate distance in meters from a given coordinate using Haversine formula.
     * This appends a distance_meters attribute when used with nearbyRaw() scope.
     */
    public function scopeNearby($query, float $latitude, float $longitude, float $radiusKm = 5)
    {
        $haversine = "(6371000 * acos(
            cos(radians({$latitude}))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians({$longitude}))
            + sin(radians({$latitude}))
            * sin(radians(latitude))
        ))";

        return $query
            ->selectRaw("*, {$haversine} AS distance_meters")
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'active')
            ->orderBy('distance_meters')
            ->having('distance_meters', '<=', $radiusKm * 1000);
    }
}
