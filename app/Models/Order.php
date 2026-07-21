<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'business_id',
        'driver_id',
        'status',
        'subtotal',
        'delivery_fee',
        'total',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'notes',
        'order_number',
        'picked_up_at',
        'on_the_way_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'delivery_lat' => 'float',
        'delivery_lng' => 'float',
    'delivery_address' => 'array',
    'subtotal' => 'decimal:2',
    'delivery_fee' => 'decimal:2',
    'total' => 'decimal:2',
];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
{
    return $this->hasMany(Order::class);
}

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
