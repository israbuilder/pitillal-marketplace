<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'business_id',
         'driver_id',
        'driver_acceptance_fee_cents',
        'driver_fee_charged_at',
        'status',
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
        'driver_acceptance_fee_cents' => 'integer',
        'driver_fee_charged_at' => 'datetime',
        'delivered_at' => 'datetime',
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

      public function getDriverAcceptanceFeeCents(): int
    {
            return (int) $this->driver_acceptance_fee_cents;
    }

    public function getFormattedDriverAcceptanceFeeAttribute(): string
    {
        return '$' . number_format(
            $this->getDriverAcceptanceFeeCents() / 100,
            2
        );
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
