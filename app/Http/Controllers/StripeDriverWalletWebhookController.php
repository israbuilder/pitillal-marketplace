<?php

namespace App\Http\Controllers;

use App\Models\DriverWalletTopUp;
use App\Services\DriverWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeDriverWalletWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        DriverWalletService $walletService,
    ): JsonResponse {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (! $signature) {
            return response()->json([
                'message' => 'Missing Stripe signature.',
            ], 400);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
        } catch (UnexpectedValueException $exception) {
            Log::warning('Invalid Stripe webhook payload.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Invalid payload.',
            ], 400);
        } catch (SignatureVerificationException $exception) {
            Log::warning('Invalid Stripe webhook signature.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Invalid signature.',
            ], 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCompletedSession(
                session: $event->data->object,
                stripeEventId: $event->id,
                walletService: $walletService,
            ),

            'checkout.session.expired' => $this->handleExpiredSession(
                session: $event->data->object,
            ),

            default => null,
        };

        return response()->json([
            'received' => true,
        ]);
    }

    private function handleCompletedSession(
        Session $session,
        string $stripeEventId,
        DriverWalletService $walletService,
    ): void {
        if (($session->metadata->type ?? null) !== 'driver_wallet_top_up') {
            return;
        }

        /*
         * Para Checkout en modo payment, verificamos que Stripe
         * indique que el pago quedó pagado.
         */
        if ($session->payment_status !== 'paid') {
            Log::info('Stripe Checkout Session is not paid yet.', [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status,
            ]);

            return;
        }

        $topUpId = $session->metadata->top_up_id ?? null;

        if (! $topUpId) {
            Log::error('Stripe wallet top-up missing top_up_id.', [
                'session_id' => $session->id,
            ]);

            return;
        }

        $topUp = DriverWalletTopUp::query()
            ->whereKey($topUpId)
            ->where(
                'stripe_checkout_session_id',
                $session->id
            )
            ->first();

        if (! $topUp) {
            Log::error('Stripe wallet top-up record not found.', [
                'session_id' => $session->id,
                'top_up_id' => $topUpId,
            ]);

            return;
        }

        /*
         * Nunca acreditamos un importe diferente al que guardamos.
         */
        if ((int) $session->amount_total !== $topUp->amount_cents) {
            Log::critical('Stripe wallet amount mismatch.', [
                'session_id' => $session->id,
                'expected_amount' => $topUp->amount_cents,
                'received_amount' => $session->amount_total,
            ]);

            return;
        }

        if (
            strtolower((string) $session->currency)
            !== strtolower($topUp->currency)
        ) {
            Log::critical('Stripe wallet currency mismatch.', [
                'session_id' => $session->id,
                'expected_currency' => $topUp->currency,
                'received_currency' => $session->currency,
            ]);

            return;
        }

        $walletService->creditTopUp(
            topUp: $topUp,
            stripeEventId: $stripeEventId,
            paymentIntentId: is_string($session->payment_intent)
                ? $session->payment_intent
                : null,
            metadata: [
                'stripe_checkout_session_id' => $session->id,
                'stripe_payment_status' => $session->payment_status,
            ],
        );
    }

    private function handleExpiredSession(
        Session $session,
    ): void {
        DriverWalletTopUp::query()
            ->where(
                'stripe_checkout_session_id',
                $session->id
            )
            ->where(
                'status',
                DriverWalletTopUp::STATUS_PENDING
            )
            ->update([
                'status' => DriverWalletTopUp::STATUS_EXPIRED,
            ]);
    }
}