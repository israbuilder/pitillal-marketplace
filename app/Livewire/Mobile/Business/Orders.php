<?php

namespace App\Livewire\Mobile\Business;

use App\Models\Business;
use App\Models\Order;
use Livewire\Component;

class Orders extends Component
{
    public Business $business;
    public string $filter = 'active';

    public function mount(): void
    {
        $this->business = Business::where('user_id', auth()->id())->firstOrFail();
    }

    public function markReady(Order $order): void
    {
        abort_unless($order->business_id === $this->business->id, 403);
        abort_unless(in_array($order->status, ['pending', 'accepted'], true), 422);
        $order->update(['status' => 'ready']);
    }

    public function render()
    {
        $query = $this->business->orders()->with(['items', 'driver'])->latest();

        if ($this->filter === 'active') {
            $query->whereNotIn('status', ['delivered', 'cancelled']);
        } elseif ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return view('livewire.mobile.business.orders', ['orders' => $query->get()])
            ->layout('components.mobile.app', ['title' => 'Pedidos del negocio', 'activeTab' => 'business']);
    }
}
