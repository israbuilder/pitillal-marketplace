<div>
    <header class="mb-7 pt-3">
        <a href="{{ route('home') }}" wire:navigate class="mb-6 inline-flex items-center gap-2 text-sm font-bold text-slate-500">← Regresar</a>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Nueva cuenta</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight">Comienza ahora</h1>
        <p class="mt-2 text-sm text-slate-500">Selecciona cómo utilizarás la plataforma.</p>
    </header>

    <form wire:submit="register" class="space-y-5">
        <fieldset>
            <legend class="mb-3 text-sm font-bold text-slate-700">Tipo de cuenta</legend>
            <div class="grid grid-cols-3 gap-2">
                @foreach (['customer' => 'Cliente', 'business' => 'Negocio', 'driver' => 'Driver'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="accountType" value="{{ $value }}" class="peer sr-only">
                        <span class="flex min-h-16 items-center justify-center rounded-2xl border border-slate-200 px-2 text-center text-xs font-bold text-slate-600 transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
            @error('accountType') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </fieldset>

        <div>
            <label for="name" class="mb-2 block text-sm font-bold">Nombre completo</label>
            <input id="name" wire:model="name" autocomplete="name" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100" placeholder="Tu nombre">
            @error('name') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="register-email" class="mb-2 block text-sm font-bold">Correo electrónico</label>
            <input id="register-email" type="email" wire:model="email" autocomplete="email" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100" placeholder="nombre@correo.com">
            @error('email') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="mb-2 block text-sm font-bold">Teléfono <span class="font-normal text-slate-400">(opcional)</span></label>
            <input id="phone" type="tel" wire:model="phone" autocomplete="tel" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100" placeholder="(713) 555-0000">
            @error('phone') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="register-password" class="mb-2 block text-sm font-bold">Contraseña</label>
            <input id="register-password" type="password" wire:model="password" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100" placeholder="Mínimo 8 caracteres">
            @error('password') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password-confirmation" class="mb-2 block text-sm font-bold">Confirmar contraseña</label>
            <input id="password-confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100" placeholder="Repite la contraseña">
        </div>

        <label class="flex items-start gap-3 text-sm leading-5 text-slate-600">
            <input type="checkbox" wire:model="terms" class="mt-0.5 h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span>Acepto los términos, condiciones y política de privacidad.</span>
        </label>
        @error('terms') <p class="text-sm font-medium text-red-600">Debes aceptar los términos.</p> @enderror

        <button type="submit" wire:loading.attr="disabled" wire:target="register" class="w-full rounded-2xl bg-indigo-600 px-4 py-4 font-bold text-white shadow-lg shadow-indigo-200 disabled:opacity-60">
            <span wire:loading.remove wire:target="register">Crear cuenta</span>
            <span wire:loading wire:target="register">Creando cuenta...</span>
        </button>
    </form>
</div>
