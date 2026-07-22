<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverLocation extends Model
{
    protected $fillable = [
        'driver_id', 
        'order_id', 
        'lat', 
        'lng', 
        'accuracy',
        'heading', 
        'speed', 
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'accuracy' => 'float',
            'heading' => 'float',
            'speed' => 'float',
            'recorded_at' => 'datetime',
        ];
    }

    public function driver(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'driver_id');
     }

    public function order(): BelongsTo 
    {
         return $this->belongsTo(Order::class); 
    }
}
