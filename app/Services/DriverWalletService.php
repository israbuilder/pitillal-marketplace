<?php

namespace App\Services;

use App\Exceptions\InsufficientDriverBalanceException;
use App\Models\DriverWallet;
use App\Models\DriverWalletTopUp;
use App\Models\DriverWalletTransaction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DriverWalletService
{
    public function getOrCreateWallet(User $driver): DriverWallet
    {
        $this->ensureDriver($driver);

        return DriverWallet::query()->firstOrCreate(
            [
                'user_id' => $driver->id,
            ],
            [
                'balance_cents' => 0,
                'currency' => 'usd',
            ]
        );
    }

    public function creditTopUp(
        DriverWalletTopUp $topUp,
        string $stripeEventId,
        ?string $paymentIntentId = null,
        array $metadata = [],
    ): DriverWalletTopUp {
        return DB::transaction(function () use (
            $topUp,
            $stripeEventId,
            $paymentIntentId,
            $metadata
        ): DriverWalletTopUp {
            $lockedTopUp = DriverWalletTopUp::query()
                ->lockForUpdate()
                ->findOrFail($topUp->id);

            /*
             * Idempotencia: si ya está pagado no vuelve a acreditar.
             */
            if ($lockedTopUp->status === DriverWalletTopUp::STATUS_PAID) {
                return $lockedTopUp;
            }

            if (
                filled($lockedTopUp->stripe_event_id)
                && $lockedTopUp->stripe_event_id !== $stripeEventId
            ) {
                throw new RuntimeException(
                    'This top-up was already processed by another Stripe event.'
                );
            }

            $wallet = DriverWallet::query()
                ->whereKey($lockedTopUp->driver_wallet_id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $wallet->balance_cents;
            $after = $before + $lockedTopUp->amount_cents;

            $wallet->update([
                'balance_cents' => $after,
            ]);

            DriverWalletTransaction::query()->create([
                'driver_wallet_id' => $wallet->id,
                'order_id' => null,
                'type' => DriverWalletTransaction::TYPE_CREDIT,
                'reason' => DriverWalletTransaction::REASON_STRIPE_TOP_UP,
                'amount_cents' => $lockedTopUp->amount_cents,
                'balance_before_cents' => $before,
                'balance_after_cents' => $after,
                'reference_type' => DriverWalletTopUp::class,
                'reference_id' => (string) $lockedTopUp->id,
                'description' => 'Driver wallet top-up through Stripe.',
                'metadata' => array_merge($metadata, [
                    'stripe_event_id' => $stripeEventId,
                    'stripe_payment_intent_id' => $paymentIntentId,
                ]),
            ]);

            $lockedTopUp->update([
                'status' => DriverWalletTopUp::STATUS_PAID,
                'stripe_event_id' => $stripeEventId,
                'stripe_payment_intent_id' => $paymentIntentId,
                'paid_at' => now(),
                'metadata' => array_merge(
                    $lockedTopUp->metadata ?? [],
                    $metadata
                ),
            ]);

            return $lockedTopUp->refresh();
        }, attempts: 3);
    }

    public function acceptOrder(
        User $driver,
        Order $order,
    ): Order {
        $this->ensureDriver($driver);

        return DB::transaction(function () use (
            $driver,
            $order
        ): Order {
            /*
             * Se bloquea el pedido para evitar que dos conductores
             * lo acepten al mismo tiempo.
             */
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->driver_id !== null) {
                throw new RuntimeException(
                    'This order has already been accepted by another driver.'
                );
            }

            /*
             * Ajusta esta lista según tus estados reales.
             */
            if (! in_array($lockedOrder->status, [
                'pending',
                'available',
                'ready_for_driver',
            ], true)) {
                throw new RuntimeException(
                    'This order is not available for acceptance.'
                );
            }

            $wallet = DriverWallet::query()
                ->where('user_id', $driver->id)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                $wallet = DriverWallet::query()->create([
                    'user_id' => $driver->id,
                    'balance_cents' => 0,
                    'currency' => 'usd',
                ]);

                /*
                 * Se vuelve a bloquear después de crearla.
                 */
                $wallet = DriverWallet::query()
                    ->whereKey($wallet->id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $feeCents = $lockedOrder->getDriverAcceptanceFeeCents();

            if ($wallet->balance_cents < $feeCents) {
                throw new InsufficientDriverBalanceException(
                    requiredCents: $feeCents,
                    availableCents: $wallet->balance_cents,
                );
            }

            $before = $wallet->balance_cents;
            $after = $before - $feeCents;

            $wallet->update([
                'balance_cents' => $after,
            ]);

            $lockedOrder->update([
                'driver_id' => $driver->id,
                'status' => 'accepted',
                'driver_fee_charged_at' => now(),
            ]);

            DriverWalletTransaction::query()->create([
                'driver_wallet_id' => $wallet->id,
                'order_id' => $lockedOrder->id,
                'type' => DriverWalletTransaction::TYPE_DEBIT,
                'reason' => DriverWalletTransaction::REASON_ORDER_ACCEPTANCE,
                'amount_cents' => $feeCents,
                'balance_before_cents' => $before,
                'balance_after_cents' => $after,
                'reference_type' => Order::class,
                'reference_id' => (string) $lockedOrder->id,
                'description' => sprintf(
                    'Fee charged for accepting order #%s.',
                    $lockedOrder->id
                ),
                'metadata' => [
                    'driver_id' => $driver->id,
                ],
            ]);

            return $lockedOrder->refresh();
        }, attempts: 3);
    }

    public function refundOrderAcceptanceFee(
        Order $order,
        string $reason = 'Order cancelled',
    ): void {
        DB::transaction(function () use ($order, $reason): void {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedOrder->driver_id) {
                return;
            }

            $originalCharge = DriverWalletTransaction::query()
                ->where('order_id', $lockedOrder->id)
                ->where(
                    'reason',
                    DriverWalletTransaction::REASON_ORDER_ACCEPTANCE
                )
                ->lockForUpdate()
                ->first();

            if (! $originalCharge) {
                return;
            }

            $existingRefund = DriverWalletTransaction::query()
                ->where('order_id', $lockedOrder->id)
                ->where(
                    'reason',
                    DriverWalletTransaction::REASON_ORDER_ACCEPTANCE_REFUND
                )
                ->exists();

            if ($existingRefund) {
                return;
            }

            $wallet = DriverWallet::query()
                ->whereKey($originalCharge->driver_wallet_id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = $wallet->balance_cents;
            $after = $before + $originalCharge->amount_cents;

            $wallet->update([
                'balance_cents' => $after,
            ]);

            DriverWalletTransaction::query()->create([
                'driver_wallet_id' => $wallet->id,
                'order_id' => $lockedOrder->id,
                'type' => DriverWalletTransaction::TYPE_REFUND,
                'reason' => DriverWalletTransaction::REASON_ORDER_ACCEPTANCE_REFUND,
                'amount_cents' => $originalCharge->amount_cents,
                'balance_before_cents' => $before,
                'balance_after_cents' => $after,
                'reference_type' => Order::class,
                'reference_id' => (string) $lockedOrder->id,
                'description' => $reason,
                'metadata' => [
                    'original_transaction_id' => $originalCharge->id,
                ],
            ]);

            $lockedOrder->update([
                'driver_id' => null,
                'driver_fee_charged_at' => null,
                'status' => 'available',
            ]);
        }, attempts: 3);
    }

    private function ensureDriver(User $user): void
    {
        if ($user->role !== 'driver') {
            throw new RuntimeException(
                'Only drivers can use the driver wallet.'
            );
        }
    }
}