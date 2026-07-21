<?php

namespace App\Livewire\Mobile\Driver;

use App\Models\DriverLocation;
use App\Models\Order;
use Livewire\Component;

class Delivery extends Component
{
    public Order $order;
    public bool $tracking = false;

    public function mount(Order $order): void
    {
        abort_unless($order->driver_id === auth()->id(), 403);
        $this->order = $order->load('business');
    }

    public function updateLocation(
        float $latitude,
        float $longitude,
        ?float $accuracy = null,
        ?float $heading = null,
        ?float $speed = null,
    ): void {
        abort_unless($this->order->driver_id === auth()->id(), 403);

        DriverLocation::create([
            'driver_id' => auth()->id(),
            'order_id' => $this->order->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'heading' => $heading,
            'speed' => $speed,
            'recorded_at' => now(),
        ]);

        $this->tracking = true;
    }

    public function markPickedUp(): void
    {
        $this->order->update(['status' => 'picked_up', 'picked_up_at' => now()]);
        $this->order->refresh();
    }

    public function startDelivery(): void
    {
        $this->order->update(['status' => 'on_the_way', 'on_the_way_at' => now()]);
        $this->order->refresh();
    }

    public function markDelivered(): void
    {
        $this->order->update(['status' => 'delivered', 'delivered_at' => now()]);
        $this->redirectRoute('driver.dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.mobile.driver.delivery')
            ->layout('components.mobile.app', ['title' => 'Entrega activa', 'activeTab' => 'driver']);
    }
}
