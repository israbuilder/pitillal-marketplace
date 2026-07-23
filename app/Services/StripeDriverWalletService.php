<?php

namespace App\Services;

use App\Models\DriverWalletTopUp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Throwable;

class StripeDriverWalletService
{
    private StripeClient $stripe;

    public function __construct(
        private readonly DriverWalletService $walletService,
    ) {
        $this->stripe = new StripeClient(
            config('services.stripe.secret')
        );
    }

    public function createCheckoutSession(
        User $driver,
        int $amountCents,
    ): Session {
        $wallet = $this->walletService->getOrCreateWallet($driver);

        $topUp = DriverWalletTopUp::query()->create([
            'user_id' => $driver->id,
            'driver_wallet_id' => $wallet->id,
            'amount_cents' => $amountCents,
            'currency' => $wallet->currency,
            'status' => DriverWalletTopUp::STATUS_PENDING,
        ]);

        try {
            /*
             * Stripe recomienda crear una nueva Checkout Session
             * para cada intento de pago.
             */
            $session = $this->stripe->checkout->sessions->create([
                'mode' => 'payment',

                'customer_email' => $driver->email,

                'client_reference_id' => (string) $topUp->id,

                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $wallet->currency,
                            'unit_amount' => $amountCents,
                            'product_data' => [
                                'name' => 'Driver wallet balance',
                                'description' => sprintf(
                                    'Wallet credit for %s',
                                    $driver->name
                                ),
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],

                'metadata' => [
                    'type' => 'driver_wallet_top_up',
                    'top_up_id' => (string) $topUp->id,
                    'driver_id' => (string) $driver->id,
                    'wallet_id' => (string) $wallet->id,
                ],

                'payment_intent_data' => [
                    'metadata' => [
                        'type' => 'driver_wallet_top_up',
                        'top_up_id' => (string) $topUp->id,
                        'driver_id' => (string) $driver->id,
                        'wallet_id' => (string) $wallet->id,
                    ],
                ],

                'success_url' => route(
                    'wallet.success',
                    [],
                    true
                ) . '?session_id={CHECKOUT_SESSION_ID}',

                'cancel_url' => route(
                    'wallet.index',
                    ['cancelled' => 1],
                    true
                ),
            ]);

            $topUp->update([
                'stripe_checkout_session_id' => $session->id,
                'metadata' => [
                    'checkout_url' => $session->url,
                ],
            ]);

            return $session;
        } catch (Throwable $exception) {
            DB::transaction(function () use (
                $topUp,
                $exception
            ): void {
                $topUp->update([
                    'status' => DriverWalletTopUp::STATUS_FAILED,
                    'metadata' => [
                        'creation_error' => $exception->getMessage(),
                    ],
                ]);
            });

            throw $exception;
        }
    }
}