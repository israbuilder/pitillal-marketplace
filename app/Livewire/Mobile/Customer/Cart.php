<?php

namespace App\Livewire\Mobile\Customer;

use Livewire\Component;

class Cart extends Component
{
    public array $items = [];

    public function mount(): void
    {
        $this->refreshCart();
    }


    private function refreshCart(): void
{
    $this->items = session()->get('cart', []);

    $this->dispatch(
        'cart-updated',
        count: $this->cartCount()
    );
}

public function cartCount(): int
{
    return collect($this->items)
        ->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
}

    public function increment(string $key): void
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
            session()->put('cart', $cart);
        }
        $this->refreshCart();
    }

    public function decrement(string $key): void
    {
        $cart = session()->get('cart', []);
        if (! isset($cart[$key])) return;

        $cart[$key]['quantity']--;
        if ($cart[$key]['quantity'] <= 0) unset($cart[$key]);
        session()->put('cart', $cart);
        $this->refreshCart();
    }

    public function remove(string $key): void
    {
        $cart = session()->get('cart', []);
        unset($cart[$key]);
        session()->put('cart', $cart);
        $this->refreshCart();
    }


    public function getSubtotalProperty(): float
    {
        return collect($this->items)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function render()
    {
        return view('livewire.mobile.customer.cart')
            ->layout('components.mobile.app', ['title' => 'Carrito', 'activeTab' => 'cart']);
    }
}
