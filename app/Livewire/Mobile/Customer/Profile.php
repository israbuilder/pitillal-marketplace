<?php

namespace App\Livewire\Mobile\Customer;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return view('livewire.mobile.profile')
            ->layout('components.mobile.app', ['title' => 'Perfil', 'activeTab' => 'profile']);
    }
}
