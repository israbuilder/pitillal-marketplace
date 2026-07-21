<?php

namespace App\Services;

use App\Models\Order;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePaymentService
{
    public function __construct(private readonly StripeClient $stripe) {}

    public static function make(): self
    {
        return new self(new StripeClient(config('services.stripe.secret')));
    }

    public function createOrReuseIntent(Order $order): PaymentIntent
    {
        if ($order->stripe_payment_intent_id) {
            return $this->stripe->paymentIntents->retrieve($order->stripe_payment_intent_id);
        }

        $intent = $this->stripe->paymentIntents->create([
            'amount' => (int) round(((float) $order->total) * 100),
            'currency' => config('services.stripe.currency', 'usd'),
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'order_id' => (string) $order->id,
                'business_id' => (string) $order->business_id,
            ],
            'description' => "Marketplace order #{$order->id}",
            'receipt_email' => $order->user?->email,
        ], [
            'idempotency_key' => "order-{$order->id}-payment-intent",
        ]);

        $order->update(['stripe_payment_intent_id' => $intent->id]);

        return $intent;
    }
}
