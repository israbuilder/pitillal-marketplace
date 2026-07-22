<?php

namespace App\Livewire\Mobile\Business;

use App\Models\Business;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public Business $business;

    public function mount(): void
    {
        $this->business = Business::where('user_id', auth()->id())->firstOrFail();
    }

    public function toggleOpen(): void
    {
        $this->business->update(['is_open' => ! $this->business->is_open]);
        $this->business->refresh();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'pending' => $this->business->orders()->whereIn('status', ['pending', 'accepted'])->count(),
            'today' => $this->business->orders()->whereDate('created_at', today())->count(),
            'sales' => (float) $this->business->orders()
                ->where('status', 'paid')->whereDate('created_at', today())->sum('total'),
            'products' => $this->business->products()->count(),
        ];
    }

    public function render()
    {
        $orders = $this->business->orders()->with(['customer', 'driver'])->latest()->limit(10)->get();

        return view('livewire.mobile.business.dashboard', compact('orders'))
            ->layout('components.mobile.app', ['title' => 'Mi negocio', 'activeTab' => 'business']);
    }
}
