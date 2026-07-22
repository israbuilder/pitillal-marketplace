<div>
    <header class="mb-8 pt-8">
        <a href="{{ route('home') }}" wire:navigate class="mb-8 inline-flex text-sm font-bold text-slate-500">← Regresar</a>
        <h1 class="text-3xl font-black tracking-tight">Recupera tu contraseña</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">Escribe tu correo y te enviaremos un enlace seguro para crear una nueva contraseña.</p>
    </header>

    @if ($status)
        <div class="mb-5 rounded-2xl bg-emerald-50 px-4 py-4 text-sm font-medium text-emerald-700">{{ $status }}</div>
    @endif

    <form wire:submit="sendResetLink" class="space-y-5">
        <div>
            <label for="forgot-email" class="mb-2 block text-sm font-bold">Correo electrónico</label>
            <input id="forgot-email" type="email" wire:model="email" autocomplete="email" autofocus class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100" placeholder="nombre@correo.com">
            @error('email') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" style="background: #E52471" wire:loading.attr="disabled" wire:target="sendResetLink" class="w-full rounded-2xl px-4 py-4 font-bold text-white shadow-lg shadow-indigo-200 disabled:opacity-60">
            <span wire:loading.remove wire:target="sendResetLink">Enviar enlace</span>
            <span wire:loading wire:target="sendResetLink">Enviando...</span>
        </button>
    </form>
</div>
