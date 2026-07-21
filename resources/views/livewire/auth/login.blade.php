<div>
    <header class="mb-8 pt-6">
        <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-600 text-2xl font-black text-white shadow-lg shadow-indigo-200">
            {{ mb_strtoupper(mb_substr(config('app.name', 'M'), 0, 1)) }}
        </div>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Bienvenido</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight">Inicia sesión</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Compra, administra tu negocio o entrega pedidos desde una sola plataforma.</p>
    </header>

    @if (session('status'))
        <div class="mb-5 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Correo electrónico</label>
            <input id="email" type="email" wire:model="email" autocomplete="email"
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                   placeholder="nombre@correo.com">
            @error('email') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between gap-4">
                <label for="password" class="block text-sm font-bold text-slate-700">Contraseña</label>
                <a href="{{ route('password.request') }}" wire:navigate class="text-sm font-bold text-indigo-600">¿La olvidaste?</a>
            </div>
            <input id="password" type="password" wire:model="password" autocomplete="current-password"
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                   placeholder="••••••••">
            @error('password') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-3 text-sm font-medium text-slate-600">
            <input type="checkbox" wire:model="remember" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            Mantener mi sesión iniciada
        </label>

        <button type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-4 py-4 font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 disabled:cursor-wait disabled:opacity-60">
            <span wire:loading.remove wire:target="login">Entrar</span>
            <span wire:loading wire:target="login">Verificando...</span>
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        ¿Todavía no tienes cuenta?
        <a href="{{ route('register') }}" wire:navigate class="font-black text-indigo-600">Regístrate</a>
    </p>
</div>
