<?php

namespace App\Livewire\Mobile\Driver;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.mobile')]
class Orders extends Component
{
    public string $tab = 'available';

    public ?int $selectedOrderId = null;

    public bool $showDetails = false;

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);

        abort_unless(
            Auth::user()->role === 'driver',
            403,
            'Esta sección es solamente para conductores.'
        );
    }

    public function setTab(string $tab): void
    {
        abort_unless(
            in_array($tab, ['available', 'active', 'completed'], true),
            404
        );

        $this->tab = $tab;
        $this->closeDetails();

        unset(
            $this->availableOrders,
            $this->activeOrders,
            $this->completedOrders
        );
    }

    #[Computed]
    public function availableOrders(): Collection
    {
        return Order::query()
            ->with([
                'business:id,name,address,phone,lat,lng',
                'customer:id,name,phone',
                'items.product:id,name,image',
            ])
            ->whereNull('driver_id')
            ->whereIn('status', [
                'pending',
                'preparing',
                'ready_for_pickup',
            ])
            ->latest()
            ->get();
    }

    #[Computed]
    public function activeOrders(): Collection
    {
        return Order::query()
            ->with([
                'business:id,name,address,phone,lat,lng',
                'customer:id,name,phone',
                'items.product:id,name,image',
            ])
            ->where('driver_id', Auth::id())
            ->whereIn('status', [
                'accepted',
                'ready_for_pickup',
                'picked_up',
            ])
            ->latest('updated_at')
            ->get();
    }

    #[Computed]
    public function completedOrders(): Collection
    {
        return Order::query()
            ->with([
                'business:id,name,address,phone',
                'customer:id,name,phone',
                'items.product:id,name,image',
            ])
            ->where('driver_id', Auth::id())
            ->where('status', 'delivered')
            ->latest('updated_at')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function selectedOrder(): ?Order
    {
        if (! $this->selectedOrderId) {
            return null;
        }

        return Order::query()
            ->with([
                'business',
                'customer',
                'items.product',
            ])
            ->whereKey($this->selectedOrderId)
            ->where(function ($query) {
                $query
                    ->whereNull('driver_id')
                    ->orWhere('driver_id', Auth::id());
            })
            ->first();
    }

    public function viewOrder(int $orderId): void
    {
        $order = Order::query()
            ->whereKey($orderId)
            ->where(function ($query) {
                $query
                    ->whereNull('driver_id')
                    ->orWhere('driver_id', Auth::id());
            })
            ->firstOrFail();

        $this->selectedOrderId = $order->id;
        $this->showDetails = true;

        unset($this->selectedOrder);
    }

    public function closeDetails(): void
    {
        $this->selectedOrderId = null;
        $this->showDetails = false;

        unset($this->selectedOrder);
    }

    public function acceptOrder(int $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($orderId);

            if ($order->driver_id !== null) {
                $this->addError(
                    'order',
                    'Otro conductor ya aceptó esta orden.'
                );

                return;
            }

            if (! in_array($order->status, [
                'pending',
                'preparing',
                'ready_for_pickup',
            ], true)) {
                $this->addError(
                    'order',
                    'Esta orden ya no está disponible.'
                );

                return;
            }

            $order->update([
                'driver_id' => Auth::id(),
                'status' => 'accepted',
            ]);
        });

        if ($this->getErrorBag()->has('order')) {
            return;
        }

        $this->tab = 'active';
        $this->selectedOrderId = $orderId;
        $this->showDetails = true;

        $this->refreshOrders();

        session()->flash(
            'success',
            'Orden aceptada correctamente.'
        );
    }

    public function markPickedUp(int $orderId): void
    {
        $order = $this->findDriverOrder($orderId);

        abort_unless(
            in_array($order->status, [
                'accepted',
                'ready_for_pickup',
            ], true),
            422,
            'La orden todavía no puede marcarse como recogida.'
        );

        $order->update([
            'status' => 'picked_up',
        ]);

        $this->refreshOrders();

        session()->flash(
            'success',
            'Pedido marcado como recogido.'
        );
    }

    public function markDelivered(int $orderId): void
    {
        $order = $this->findDriverOrder($orderId);

        abort_unless(
            $order->status === 'picked_up',
            422,
            'Primero debes marcar el pedido como recogido.'
        );

        $order->update([
            'status' => 'delivered',
        ]);

        $this->closeDetails();
        $this->tab = 'completed';

        $this->refreshOrders();

        session()->flash(
            'success',
            'Pedido entregado correctamente.'
        );
    }

    public function releaseOrder(int $orderId): void
    {
        $order = $this->findDriverOrder($orderId);

        abort_unless(
            $order->status === 'accepted',
            422,
            'Ya no puedes liberar esta orden.'
        );

        $order->update([
            'driver_id' => null,
            'status' => 'ready_for_pickup',
        ]);

        $this->closeDetails();
        $this->tab = 'available';

        $this->refreshOrders();

        session()->flash(
            'success',
            'La orden volvió a estar disponible.'
        );
    }

    protected function findDriverOrder(int $orderId): Order
    {
        return Order::query()
            ->whereKey($orderId)
            ->where('driver_id', Auth::id())
            ->firstOrFail();
    }

    protected function refreshOrders(): void
    {
        unset(
            $this->availableOrders,
            $this->activeOrders,
            $this->completedOrders,
            $this->selectedOrder
        );
    }

    public function render()
    {
        return view('livewire.mobile.driver.orders');
    }
}