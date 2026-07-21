<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.product.business', 'business', 'user'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $order->load(['items.product.business', 'business', 'user']);

        if ($order->user_id !== $request->user()->id) {
            abort(403, 'No tienes permiso para ver esta orden.');
        }

        return response()->json([
            'data' => $order,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'delivery_address' => ['required', 'array'],
            'delivery_address.full_name' => ['required', 'string', 'max:255'],
            'delivery_address.phone' => ['required', 'string', 'max:50'],
            'delivery_address.address_line_1' => ['required', 'string', 'max:255'],
            'delivery_address.address_line_2' => ['nullable', 'string', 'max:255'],
            'delivery_address.city' => ['required', 'string', 'max:255'],
            'delivery_address.notes' => ['nullable', 'string', 'max:1000'],
            'delivery_address.latitude' => ['nullable', 'numeric'],
            'delivery_address.longitude' => ['nullable', 'numeric'],

            'payment_method' => ['nullable', 'in:cash,card'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($data, $request) {
            $productIds = collect($data['items'])->pluck('product_id')->unique()->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            $businessIds = $products
                ->pluck('business_id')
                ->unique()
                ->values();

            if ($businessIds->count() !== 1) {
                throw ValidationException::withMessages([
                    'items' => ['No puedes mezclar productos de diferentes negocios en una misma orden.'],
                ]);
            }

            $businessId = $businessIds->first();

            $subtotal = 0;

            $order = Order::create([
                'user_id' => $request->user()->id,
                'business_id' => $businessId,
                'status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'cash',

                'delivery_address' => $data['delivery_address'],
                'delivery_lat' => $data['delivery_address']['latitude'] ?? null,
                'delivery_lng' => $data['delivery_address']['longitude'] ?? null,

                'subtotal' => 0,
                'delivery_fee' => 0,
                'total' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => ['Uno de los productos no existe.'],
                    ]);
                }

                $unitPrice = (float) $product->price;
                $quantity = (int) $item['quantity'];
                $lineTotal = $unitPrice * $quantity;

                $subtotal += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $deliveryFee = 5.00;

            $order->update([
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal + $deliveryFee,
            ]);

            return $order->load(['items.product.business', 'business', 'user']);
        });

        return response()->json([
            'data' => $order,
        ], 201);
    }

    public function businessOrders(Request $request)
    {
        $businessId = $request->user()->business?->id;

        if (! $businessId) {
            abort(403, 'Este usuario no tiene un negocio asignado.');
        }

        $orders = Order::query()
            ->with(['items.product.business', 'business', 'user'])
            ->where('business_id', $businessId)
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function availableForDriver()
    {
        $orders = Order::query()
            ->with(['items.product.business', 'business', 'user'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function acceptDriverOrder(Request $request, Order $order)
    {
        if (! in_array($order->status, ['pending', 'confirmed'], true)) {
            throw ValidationException::withMessages([
                'order' => ['Esta orden ya no está disponible.'],
            ]);
        }

        $order->update([
            'driver_id' => $request->user()->id,
            'status' => 'assigned',
        ]);

        return response()->json([
            'data' => $order->load(['items.product.business', 'business', 'user']),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,assigned,picked_up,delivered,cancelled'],
        ]);

        $order->update([
            'status' => $data['status'],
        ]);

        return response()->json([
            'data' => $order->load(['items.product.business', 'business', 'user']),
        ]);
    }
}