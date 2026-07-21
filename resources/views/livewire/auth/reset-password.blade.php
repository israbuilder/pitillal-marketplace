<div>
    <header class="mb-8 pt-8">
        <h1 class="text-3xl font-black tracking-tight">Crea una nueva contraseña</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">La nueva contraseña debe ser segura y diferente de la anterior.</p>
    </header>

    <form wire:submit="resetPassword" class="space-y-5">
        <div>
            <label for="reset-email" class="mb-2 block text-sm font-bold">Correo electrónico</label>
            <input id="reset-email" type="email" wire:model="email" autocomplete="email" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            @error('email') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="new-password" class="mb-2 block text-sm font-bold">Nueva contraseña</label>
            <input id="new-password" type="password" wire:model="password" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            @error('password') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="new-password-confirmation" class="mb-2 block text-sm font-bold">Confirmar contraseña</label>
            <input id="new-password-confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="resetPassword" class="w-full rounded-2xl bg-indigo-600 px-4 py-4 font-bold text-white shadow-lg shadow-indigo-200 disabled:opacity-60">
            <span wire:loading.remove wire:target="resetPassword">Guardar contraseña</span>
            <span wire:loading wire:target="resetPassword">Guardando...</span>
        </button>
    </form>
</div>
