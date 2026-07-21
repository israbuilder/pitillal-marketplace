<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Iniciar sesión')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    public function login()
    {
        $credentials = $this->validate();

        unset($credentials['remember']);

        if (! Auth::attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'El correo electrónico o la contraseña no son correctos.',
            ]);
        }

        request()->session()->regenerate();

        $user = Auth::user();

        $routeName = match ($user->role) {
            'customer' => 'customer.home',
            'business' => 'business.dashboard',
            'driver' => 'driver.dashboard',
            default => null,
        };

        if ($routeName === null) {
            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Tu cuenta no tiene un rol válido.',
            ]);
        }

        return $this->redirectRoute(
            $routeName,
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}