<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance_cents',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'balance_cents' => 'integer',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(DriverWalletTransaction::class);
    }

    public function topUps(): HasMany
    {
        return $this->hasMany(DriverWalletTopUp::class);
    }

    public function hasEnoughBalance(int $amountCents): bool
    {
        return $this->balance_cents >= $amountCents;
    }

    public function getFormattedBalanceAttribute(): string
    {
        return '$' . number_format($this->balance_cents / 100, 2). ' MXN';
    }
}