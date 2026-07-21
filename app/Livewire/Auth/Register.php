<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Business;
use App\Support\RedirectsUsersByRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Crear cuenta')]
class Register extends Component
{
    use RedirectsUsersByRole;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $accountType = 'customer';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'accountType' => ['required', 'in:customer,business,driver'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ];
    }

    public function register(): void
    {
        $validated = $this->validate();

        $user = DB::transaction(function () use ($validated): User {
            $attributes = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ];

            if ($validated['phone'] !== '' && in_array('phone', (new User())->getFillable(), true)) {
                $attributes['phone'] = $validated['phone'];
            }

            $user = User::create($attributes);
            $this->assignRole($user, $validated['accountType']);

        if ($validated['accountType'] === 'business') {
            Business::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] !== ''
                    ? $validated['phone']
                    : null,
            ]);
        }
            
            return $user->fresh();
        });

        event(new Registered($user));
        Auth::login($user);
        request()->session()->regenerate();

        $this->redirectRoute($this->redirectRouteFor($user), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
