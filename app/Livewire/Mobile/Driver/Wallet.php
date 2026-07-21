<?php

namespace App\Livewire\Mobile\Driver;

use App\Models\DriverWallet;
use App\Models\DriverWalletTransaction;
use App\Models\User;
use App\Services\DriverWalletService;
use App\Services\StripeDriverWalletService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use RuntimeException;
use Throwable;

// #[Layout('components.layouts.mobile')]
class Wallet extends Component
{
    public string $amount = '25.00';

    public array $presetAmounts = [
        10,
        25,
        50,
        100,
    ];

    public function mount(
        DriverWalletService $walletService,
    ): void {
        $walletService->getOrCreateWallet($this->driver());
    }

    public function selectAmount(int $amount): void
    {
        if (! in_array($amount, $this->presetAmounts, true)) {
            return;
        }

        $this->amount = number_format($amount, 2, '.', '');
    }

    public function purchaseBalance(
        StripeDriverWalletService $stripeWalletService,
    ) {
        $validated = $this->validate([
            'amount' => [
                'required',
                'numeric',
                'min:' . (
                    config(
                        'services.driver_wallet.minimum_top_up_cents',
                        1000
                    ) / 100
                ),
                'max:' . (
                    config(
                        'services.driver_wallet.maximum_top_up_cents',
                        50000
                    ) / 100
                ),
            ],
        ], [
            'amount.required' => 'Enter an amount.',
            'amount.numeric' => 'The amount must be numeric.',
            'amount.min' => 'The minimum top-up is $10.00.',
            'amount.max' => 'The maximum top-up is $500.00.',
        ]);

        $amountCents = (int) round(
            ((float) $validated['amount']) * 100
        );

        try {
            $session = $stripeWalletService->createCheckoutSession(
                driver: $this->driver(),
                amountCents: $amountCents,
            );

            if (! $session->url) {
                throw new RuntimeException(
                    'Stripe did not return a Checkout URL.'
                );
            }

            /*
             * Navegación externa: no se usa wire:navigate.
             */
            return $this->redirect(
                $session->url,
                navigate: false
            );
        } catch (Throwable $exception) {
            report($exception);

            $this->addError(
                'amount',
                'We could not start the payment. Please try again.'
            );

            return null;
        }
    }

    public function render(): View
    {
        $wallet = DriverWallet::query()
            ->where('user_id', $this->driver()->id)
            ->firstOrFail();

        $transactions = DriverWalletTransaction::query()
            ->where('driver_wallet_id', $wallet->id)
            ->latest()
            ->limit(25)
            ->get();

        return view('livewire.mobile.driver.wallet', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ])->layout('components.mobile.app', [
            'title' => 'Driver Wallet',
        ]);
    }

    private function driver(): User
    {
        $user = auth()->user();

        abort_unless(
            $user instanceof User && $user->role === 'driver',
            403
        );

        return $user;
    }
}