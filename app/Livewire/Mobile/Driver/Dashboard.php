<?php

namespace App\Livewire\Mobile\Driver;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use RuntimeException;
use App\Services\DriverWalletService;
use Throwable;

class Dashboard extends Component
{
    public bool $online = false;

    public Collection $availableOrders;

    public function mount(): void
    {
        $driver = $this->driver();
       
        $this->online = (bool) $driver->is_online;
      
        $this->refreshAvailableOrders();
    }

    public function refreshAvailableOrders(): void
    {
        if (! $this->online) {
            $this->availableOrders = new Collection();

            return;
        }
          $driver = $this->driver();
        $walletBalance = (int) ($driver->driverWallet?->balance_cents ?? 0);
        if ($walletBalance <= 50) {
            $this->availableOrders = new Collection();

            return;
        }
        $this->availableOrders = Order::query()
             ->whereIn('status', [
                'pending',
                'ready',
                'preparing', 
            ])
            ->whereNull('driver_id')
            ->latest()
            ->limit(20)
            ->get();
    }

    public function toggleOnline(): void
    {
         $driver = $this->driver();
         $newStatus = ! $this->online;
         $driver->is_online = $newStatus;
         $driver->last_seen_at = now();
         $driver->save();
         $this->online = $newStatus;
         $this->refreshAvailableOrders();
    }

    public function accept(int $orderId,  DriverWalletService $walletService,): void
    {
       
        try {
            DB::transaction(function () use ($orderId, $walletService): void {
                $driver = $this->driver();
                $order = Order::query()
                    ->whereKey($orderId)
                    ->lockForUpdate()
                    ->firstOrFail();
                if ($order->status !== 'ready' && $order->status !== 'pending') {
                  session()->flash(
                                            'error',
                                            'Este pedido ya no está disponible.',   
                                       );
                    throw new RuntimeException(
                        'Este pedido ya no está disponible.'
                    );
                  
                }

                if ($order->driver_id !== null) {
                    session()->flash(
                                            'error',
                                            'Otro conductor ya aceptó este pedido.',   
                                       );
                    throw new RuntimeException(
                        'Otro conductor ya aceptó este pedido.'
                    );
                }
                              

                $requiredBalance = (int) (
                    $order->delivery_fee
                    ?? $order->driver_earning_cents
                    ?? 0
                );

                $currentBalance = (int) (
                    $driver->driverWallet->balance_cents ?? 0
                );
                if ($currentBalance < $requiredBalance) {
                     session()->flash(
                                       'error',
                                       'No tienes saldo suficiente para aceptar este pedido.',                                    
                                    );
                    throw new RuntimeException(
                        'No tienes saldo suficiente para aceptar este pedido.'
                    );
                
                }
                $walletService->acceptOrder(
                driver: $driver,
                order: $order,
            );
 
                $order->update([
                    'driver_id' => $driver->id,
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);
            });

            $this->refreshAvailableOrders();

            session()->flash(
                'success',
                'Pedido aceptado correctamente.'
            );

            $this->redirectRoute(
                'driver.orders',
                navigate: true
            );
           
        } catch (Throwable $exception) {
            report($exception);

              logger()->error('Error accepting driver order', [
        'order_id' => $orderId,
        'exception_class' => get_class($exception),
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
    ]);

    session()->flash(
        'error',
        app()->isLocal()
            ? $exception->getMessage()
            : 'No pudimos aceptar el pedido.'
    );

            $this->addError(
                'accept',
                $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'No pudimos aceptar el pedido.'
            );

            $this->refreshAvailableOrders();
        }
    }

    private function driver()
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'driver', 403);

        /*
         * Usa esto si el usuario mismo contiene
         * los campos del conductor.
         */
        return $user;

        /*
         * Si tienes relación driver:
         *
         * return $user->driver()->firstOrFail();
         */
    }

    public function render(): View
    {
        return view('livewire.mobile.driver.dashboard')
            ->layout('components.mobile.app', [
                'title' => 'Pedidos disponibles',
                'activeTab' => 'home',
            ]);
    }
}