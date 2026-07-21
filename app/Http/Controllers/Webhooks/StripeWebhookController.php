<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(): Response
    {
        try {
            $event = Webhook::constructEvent(
                request()->getContent(),
                request()->header('Stripe-Signature'),
                config('services.stripe.webhook_secret'),
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response('Invalid webhook', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            Order::where('stripe_payment_intent_id', $intent->id)->update([
                'payment_status' => 'paid',
                'status' => 'pending',
            ]);
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $intent = $event->data->object;
            Order::where('stripe_payment_intent_id', $intent->id)
                ->update(['payment_status' => 'failed']);
        }

        return response('ok');
    }
}
