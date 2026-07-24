<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Order;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
 use HasApiTokens, HasFactory, Notifiable;
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'phone',
    'address',
    'apartment',
    'city',
    'state',
    'zip_code',
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
             'is_online' => 'boolean',
             'last_seen_at' => 'datetime',
        ];
    }

    public function locations()
{
    return $this->hasMany(DriverLocation::class, 'driver_id');
}

public function driverProfile(): HasOne
{
    return $this->hasOne(DriverProfile::class);
}

public function latestLocation()
{
    return $this->hasOne(DriverLocation::class, 'driver_id')
        ->latestOfMany();
}

    public function orders():HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
        
        }

 public function driverOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function driverWallet(): HasOne
    {
        return $this->hasOne(
            DriverWallet::class,
            'user_id'
        );
    }

    public function walletTopUps(): HasMany
    {
        return $this->hasMany(
            DriverWalletTopUp::class,
            'user_id'
        );
    }

    public function getOrCreateDriverWallet(): DriverWallet
    {
        return $this->driverWallet()->firstOrCreate(
            [],
            [
                'balance_cents' => 0,
                'currency' => 'mxn',
            ]
        );
    }

}
