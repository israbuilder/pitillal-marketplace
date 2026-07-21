<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public function logout(): void
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirectRoute('home', navigate: true);
    }

    public function render()
    {
        return <<<'HTML'
            <button
                type="button"
                wire:click="logout"
                class="w-full rounded-2xl px-4 py-3 text-left text-sm font-semibold text-red-600 hover:bg-red-50"
            >
                Cerrar sesión
            </button>
        HTML;
    }
}
