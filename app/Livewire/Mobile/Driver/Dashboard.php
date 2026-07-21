<?php

namespace App\Livewire\Mobile\Driver;

use App\Models\Order;
use Livewire\Component;

class Dashboard extends Component
{
    public bool $online = true;

    public function toggleOnline(): void
    {
        $this->online = ! $this->online;
    }

    public function accept(int $orderId): void
    {
        $order = Order::query()->where('status', 'pending')->findOrFail($orderId);

        $order->update([
            'driver_id' => auth()->id(),
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $this->redirectRoute('driver.delivery', $order, navigate: true);
    }

    public function render()
    {
        $availableOrders = Order::query()
            ->whereNull('driver_id')
            ->where('status', 'pending')
            ->with('business')
            ->latest()
            ->limit(20)
            ->get();

        return view('livewire.mobile.driver.dashboard', compact('availableOrders'))
            ->layout('components.mobile.app', ['title' => 'Pedidos disponibles', 'activeTab' => 'driver']);
    }
}
