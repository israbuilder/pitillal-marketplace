<?php

namespace App\Livewire\Mobile\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Checkout extends Component
{
    /**
     * Productos almacenados en la sesión.
     */
    public array $items = [];

    /**
     * Información del cliente.
     */
    public string $customerName = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $apartment = '';

    public string $city = '';

    public string $state = 'TX';

    public string $zipCode = '';

    public string $deliveryInstructions = '';

    public string $notes = '';

    /**
     * Método de pago.
     */
    public string $paymentMethod = 'cash';

    /**
     * Totales.
     */
    public float $subtotal = 0;

    public float $deliveryFee = 0;

    public float $tax = 0;

    public float $total = 0;

    public bool $processing = false;

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);

        abort_unless(
            Auth::user()->role === 'customer',
            403
        );

        $this->customerName = (string) Auth::user()->name;
        $this->email = (string) (Auth::user()->email ?? '');
        $this->phone = (string) (Auth::user()->phone ?? '');
        $this->address = (string) (Auth::user()->address ?? '');
        $this->apartment = (string) (Auth::user()->apartment ?? '');
        $this->city = (string) (Auth::user()->city ?? '');
        $this->state = (string) (Auth::user()->state ?? 'JAL');
        $this->zipCode = (string) (Auth::user()->zip_code ?? '');

        $this->refreshCart();

        if ($this->items === []) {
            $this->redirectRoute(
                'customer.cart',
                navigate: true
            );

            return;
        }

        $this->calculateTotals();
    }

    protected function rules(): array
    {
        return [
            'customerName' => [
                'required',
                'string',
                'min:2',
                'max:150',
            ],

            'phone' => [
                'required',
                'string',
                'min:7',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'address' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],

            'apartment' => [
                'nullable',
                'string',
                'max:50',
            ],

            'city' => [
                'required',
                'string',
                'max:100',
            ],

            'state' => [
                'required',
                'string',
                'size:2',
            ],

            'zipCode' => [
                'required',
                'string',
                'max:10',
            ],

            'deliveryInstructions' => [
                'nullable',
                'string',
                'max:500',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'paymentMethod' => [
                'required',
                Rule::in([
                    'cash',
                    'card',
                    'cash_on_delivery',
                ]),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'customerName.required' => 'Escribe tu nombre.',
            'phone.required' => 'Escribe tu número de teléfono.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'address.required' => 'Escribe la dirección de entrega.',
            'city.required' => 'Escribe la ciudad.',
            'state.required' => 'Escribe el estado.',
            'state.size' => 'El estado debe tener dos letras.',
            'zipCode.required' => 'Escribe el código postal.',
            'paymentMethod.required' => 'Selecciona un método de pago.',
            'paymentMethod.in' => 'El método de pago seleccionado no es válido.',
        ];
    }

    /**
     * Carga el carrito desde la sesión.
     */
    private function refreshCart(): void
    {
        $this->items = session()->get('cart', []);
    }

    /**
     * Calcula los totales mostrados al cliente.
     */
    private function calculateTotals(): void
    {
        $this->subtotal = collect($this->items)
            ->sum(function (array $item): float {
                $price = (float) ($item['price'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                return $price * $quantity;
            });

        /*
         * Puedes reemplazar esta tarifa por una calculada
         * según distancia, negocio o zona postal.
         */
        $this->deliveryFee = $this->subtotal > 0
            ? 5.00
            : 0.00;

        /*
         * Por ahora no calculamos impuestos.
         * Ajusta esta parte según las reglas del negocio.
         */
        $this->tax = 0.00;

        $this->total = round(
            $this->subtotal +
            $this->deliveryFee +
            $this->tax,
            2
        );
    }

    /**
     * Crea la orden.
     */
    public function placeOrder(): void
    {

   
        if ($this->processing) {
            return;
        }

        $this->processing = true;

        try {
            $this->validate();

            $cart = session()->get('cart', []);

            if ($cart === []) {
                $this->addError(
                    'cart',
                    'Tu carrito está vacío.'
                );

                return;
            }

            $cartBusinessId = session()->get(
                'cart_business_id'
            );

            if (! $cartBusinessId) {
                $this->addError(
                    'cart',
                    'No se pudo identificar el negocio del pedido.'
                );

                return;
            }

            $productIds = collect($cart)
                ->pluck('product_id')
                ->filter()
                ->map(fn ($id): int => (int) $id)
                ->values()
                ->all();

            if ($productIds === []) {
                $this->addError(
                    'cart',
                    'El carrito no contiene productos válidos.'
                );

                return;
            }

            $order = DB::transaction(function () use (
                $cart,
                $cartBusinessId,
                $productIds
            ): Order {
                /*
                 * Bloqueamos los productos mientras se genera
                 * la orden para leer precios válidos.
                 */
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->where('business_id', $cartBusinessId)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->count() !== count($productIds)) {
                    throw new \RuntimeException(
                        'Uno o más productos ya no están disponibles.'
                    );
                }

                $validatedItems = $this->buildOrderItems(
                    $cart,
                    $products,
                    (int) $cartBusinessId
                );

                $subtotal = collect($validatedItems)
                    ->sum('line_total');

                $deliveryFee = $subtotal > 0
                    ? 5.00
                    : 0.00;

                $tax = 0.00;

                $total = round(
                    $subtotal + $deliveryFee + $tax,
                    2
                );

                $order = Order::query()->create([
                    /*
                     * Esta versión supone que customer_id
                     * apunta a users.id.
                     */
                    'user_id' => Auth::id(),

                    'business_id' => (int) $cartBusinessId,

                    'order_number' => $this->generateOrderNumber(),

                    'status' => 'pending',

                    'payment_status' => 'pending',

                    'payment_method' => $this->paymentMethod,

                    'delivery_address' => trim($this->address),

                    'notes' => trim($this->notes) ?: null,

                    'subtotal' => round($subtotal, 2),

                    'delivery_fee' => round($deliveryFee, 2),

                    'tax' => round($tax, 2),

                    'total' => round($total, 2),
                ]);

               Auth::user()->update([
                    'address' => $this->address,
                    'apartment' => $this->apartment,
                    'city' => $this->city,
                    'state' => $this->state,
                    'zip_code' => $this->zipCode,
                    'phone' => $this->phone,
                ]);

              

                foreach ($validatedItems as $item) {
                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'line_total' => $item['line_total'],
                    ]);
                }

                return $order;
            });

            session()->forget([
                'cart',
                'cart_business_id',
            ]);

            $this->items = [];

            $this->dispatch(
                'cart-updated',
                count: 0
            );

            session()->flash(
                'success',
                'Tu pedido fue creado correctamente.'
            );

            $this->redirectRoute(
                'customer.orders.show',
                ['order' => $order->id],
                navigate: true
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            $this->addError(
                'checkout',
                app()->isLocal()
                    ? $exception->getMessage()
                    : 'No pudimos crear el pedido. Intenta nuevamente.'
            );
        } finally {
            $this->processing = false;
        }
    }

    /**
     * Construye los artículos usando los precios reales de la DB.
     */
    private function buildOrderItems(
        array $cart,
        Collection $products,
        int $businessId
    ): array {
        $orderItems = [];

        foreach ($cart as $cartItem) {
            $productId = (int) (
                $cartItem['product_id'] ?? 0
            );

            $quantity = max(
                1,
                (int) ($cartItem['quantity'] ?? 1)
            );

            /** @var Product|null $product */
            $product = $products->get($productId);

            if (! $product) {
                throw new \RuntimeException(
                    'Uno o más productos ya no están disponibles.'
                );
            }

            if (
                (int) $product->business_id !==
                $businessId
            ) {
                throw new \RuntimeException(
                    'El carrito contiene productos de diferentes negocios.'
                );
            }

            $unitPrice = round(
                (float) $product->price,
                2
            );

            $lineTotal = round(
                $unitPrice * $quantity,
                2
            );

            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }

        return $orderItems;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-'.now()->format('Ymd-His').'-'.Str::upper(
            Str::random(5)
        );
    }

    public function render()
    {
        return view(
            'livewire.mobile.customer.checkout'
        )->layout(
            'components.mobile.app',
            [
                'title' => 'Finalizar compra',
                'activeTab' => 'cart',
            ]
        );
    }
}