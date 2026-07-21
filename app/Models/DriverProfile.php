<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'vehicle_type',
        'vehicle_model',
        'vehicle_color',
        'plate_number',
        'license_number',
        'profile_photo',
        'is_approved',
        'lat',
        'lng',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}