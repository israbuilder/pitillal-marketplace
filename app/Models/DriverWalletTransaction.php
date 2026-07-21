<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverWalletTransaction extends Model
{
    use HasFactory;

    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';
    public const TYPE_REFUND = 'refund';
    public const TYPE_ADJUSTMENT = 'adjustment';

    public const REASON_STRIPE_TOP_UP = 'stripe_top_up';
    public const REASON_ORDER_ACCEPTANCE = 'order_acceptance';
    public const REASON_ORDER_ACCEPTANCE_REFUND = 'order_acceptance_refund';
    public const REASON_ADMIN_ADJUSTMENT = 'admin_adjustment';

    protected $fillable = [
        'driver_wallet_id',
        'order_id',
        'type',
        'reason',
        'amount_cents',
        'balance_before_cents',
        'balance_after_cents',
        'reference_type',
        'reference_id',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'balance_before_cents' => 'integer',
            'balance_after_cents' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(
            DriverWallet::class,
            'driver_wallet_id'
        );
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount_cents / 100, 2);
    }
}