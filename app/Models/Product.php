<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
        'stock',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // Relationships
    public function shop(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope for available products
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
