<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripePaymentController extends Controller
{
    public function intent(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->payment_method === 'card', 422);
        abort_if($order->payment_status === 'paid', 422, 'El pedido ya está pagado.');

        $intent = StripePaymentService::make()->createOrReuseIntent($order);

        return response()->json([
            'clientSecret' => $intent->client_secret,
            'orderId' => $order->id,
        ]);
    }
}
