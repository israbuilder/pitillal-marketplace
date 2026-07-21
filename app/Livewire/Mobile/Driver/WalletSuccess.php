<?php

namespace App\Livewire\Mobile\Driver;

use App\Models\DriverWalletTopUp;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.mobile')]
class WalletSuccess extends Component
{
    public ?DriverWalletTopUp $topUp = null;

    public function mount(): void
    {
        $sessionId = request()->query('session_id');

        if (! is_string($sessionId) || blank($sessionId)) {
            return;
        }

        $this->topUp = DriverWalletTopUp::query()
            ->where('user_id', auth()->id())
            ->where('stripe_checkout_session_id', $sessionId)
            ->first();
    }

    public function render(): View
    {
        return view('livewire.mobile.driver.wallet-success');
    }
}