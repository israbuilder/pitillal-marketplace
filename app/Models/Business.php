<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'phone', 'email', 'logo_path', 'cover_path',
        'description', 'address', 'lat', 'lng', 'is_open', 'is_active',
        'delivery_fee', 'estimated_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'is_active' => 'boolean',
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'delivery_fee' => 'decimal:2',
        ];
    }

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
}
