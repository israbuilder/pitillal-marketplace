<?php

namespace App\Livewire\Mobile\Customer;

use App\Models\Product;
use Livewire\Component;

class ProductShow extends Component
{
    public Product $product;

    public int $quantity = 1;

    public function mount(Product $product): void
    {
        abort_unless($product->is_active, 404);

        $this->product = $product->load('business');
    }

    public function incrementQuantity(): void
    {
        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $cart = session()->get('cart', []);

        $currentBusinessId = session()->get('cart_business_id');

        if (
            $currentBusinessId !== null &&
            (int) $currentBusinessId !== (int) $this->product->business_id
        ) {
            $cart = [];
        }

        $existingQuantity = $cart[$this->product->id]['quantity'] ?? 0;

        $cart[$this->product->id] = [
            'product_id' => $this->product->id,
            'business_id' => $this->product->business_id,
            'name' => $this->product->name,
            'price' => (float) $this->product->price,
            'quantity' => $existingQuantity + $this->quantity,
            'image' => $this->product->image
                ?? $this->product->image_path
                ?? null,
        ];

        session()->put('cart', $cart);
        session()->put(
            'cart_business_id',
            $this->product->business_id
        );

        $this->dispatch(
            'cart-updated',
            count: collect($cart)->sum(
                fn (array $item): int =>
                    (int) ($item['quantity'] ?? 0)
            )
        );

        session()->flash(
            'success',
            "{$this->product->name} fue agregado al carrito."
        );

        $this->quantity = 1;
    }

    public function render()
    {
        return view('livewire.mobile.customer.product-show')
        ->layout('components.mobile.app', ['title' => 'Producto', 'activeTab' => 'products']);
    }
}