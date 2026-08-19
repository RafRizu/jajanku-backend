<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'driver_id',
        'delivery_type',
        'status',
        'total_price',
        'delivery_fee',
        'delivery_address',
        'latitude',
        'longitude',
        'driver_latitude',
        'driver_longitude',
        'notes',
    ];

    protected $casts = [
        'total_price'      => 'decimal:2',
        'delivery_fee'     => 'decimal:2',
        'latitude'         => 'float',
        'longitude'        => 'float',
        'driver_latitude'  => 'float',
        'driver_longitude' => 'float',
    ];

    // Status constants
    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ON_DELIVERY = 'on_delivery';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    // Relationships
    public function buyer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Status label helper
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Menunggu Konfirmasi',
            'confirmed'   => 'Dikonfirmasi',
            'processing'  => 'Sedang Diproses',
            'on_delivery' => 'Dalam Pengiriman',
            'delivered'   => 'Selesai',
            'cancelled'   => 'Dibatalkan',
            default       => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'warning',
            'confirmed'   => 'info',
            'processing'  => 'primary',
            'on_delivery' => 'orange',
            'delivered'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }
}
