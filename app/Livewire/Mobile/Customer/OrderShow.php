<?php

namespace App\Livewire\Mobile\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderShow extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        abort_unless(Auth::check(), 401);

        abort_unless(
            (int) $order->user_id === (int) Auth::id(),
            403
        );

        $this->order = $order->load([
            'items.product',
            'business',
            'driver',
        ]);
    }

    public function refreshOrder(): void
    {
        $this->order->refresh();

        $this->order->load([
            'items.product',
            'business',
            'driver',
        ]);
    }

    public function render()
    {
        return view(
            'livewire.mobile.customer.order-show'
        )->layout(
            'components.mobile.app',
            [
                'title' => 'Seguimiento del pedido',
                'activeTab' => 'orders',
            ]
        );
    }
}