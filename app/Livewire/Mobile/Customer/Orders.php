<?php

namespace App\Livewire\Mobile\Customer;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Mis pedidos')]
class Orders extends Component
{
    use WithPagination;

    public string $status = 'all';

    public string $search = '';

    public int $perPage = 10;

    public array $statuses = [
        'all' => 'Todos',
        'awaiting_payment' => 'Esperando pago',
        'pending' => 'Pendientes',
        'accepted' => 'Aceptados',
        'ready_for_pickup' => 'Listos',
        'driver_assigned' => 'Driver asignado',
        'picked_up' => 'Recogidos',
        'on_the_way' => 'En camino',
        'delivered' => 'Entregados',
        'cancelled' => 'Cancelados',
    ];

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function selectStatus(string $status): void
    {
        abort_unless(array_key_exists($status, $this->statuses), 422);

        $this->status = $status;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status = 'all';
        $this->search = '';

        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        return Order::query()
            ->with([
                'business',
                'items.product',
                'driver',
            ])
            ->where('user_id', Auth::id())
            ->when(
                $this->status !== 'all',
                fn (Builder $query) => $query
                    ->where('status', $this->status)
            )
            ->when(
                trim($this->search) !== '',
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(function (Builder $subquery) use ($search): void {
                        $subquery
                            ->where('id', $search)
                            ->orWhere('order_number', 'like', "%{$search}%")
                            ->orWhereHas(
                                'business',
                                fn (Builder $businessQuery) => $businessQuery
                                    ->where('name', 'like', "%{$search}%")
                            );
                    });
                }
            )
            ->latest()
            ->paginate($this->perPage);
    }

    public function statusLabel(string $status): string
    {
        return $this->statuses[$status]
            ?? str($status)->replace('_', ' ')->title()->toString();
    }

    public function statusClasses(string $status): string
    {
        return match ($status) {
            'awaiting_payment' => 'bg-amber-100 text-amber-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            'accepted' => 'bg-blue-100 text-blue-700',
            'ready_for_pickup' => 'bg-indigo-100 text-indigo-700',
            'driver_assigned' => 'bg-violet-100 text-violet-700',
            'picked_up' => 'bg-purple-100 text-purple-700',
            'on_the_way' => 'bg-cyan-100 text-cyan-700',
            'delivered' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function paymentStatusLabel(?string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'paid' => 'Pagado',
            'failed' => 'Pago fallido',
            'cash_due' => 'Pago al entregar',
            'refunded' => 'Reembolsado',
            default => 'Pendiente de pago',
        };
    }

    public function paymentStatusClasses(?string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'paid' => 'text-emerald-600',
            'failed' => 'text-red-600',
            'cash_due' => 'text-blue-600',
            'refunded' => 'text-purple-600',
            default => 'text-amber-600',
        };
    }

    public function canTrack(Order $order): bool
    {
        return in_array($order->status, [
            'driver_assigned',
            'picked_up',
            'on_the_way',
        ], true);
    }

    public function render()
    {
        abort_unless(Auth::check(), 401);
        abort_unless(Auth::user()->role === 'customer', 403);

        return view('livewire.mobile.customer.orders')
          ->layout('components.mobile.app', ['title' => 'Pedidos', 'activeTab' => 'Orders']);
    }
}