<div class="space-y-4">
    <div class="flex items-center gap-4 rounded-3xl bg-slate-100 p-5">
        <div class="grid h-16 w-16 place-items-center rounded-full bg-indigo-600 text-2xl font-bold text-white">{{ strtoupper(substr(auth()->user()->name ?? 'I', 0, 1)) }}</div>
        <div><h2 class="text-lg font-bold">{{ auth()->user()->name ?? 'Invitado' }}</h2><p class="text-sm text-slate-500">{{ auth()->user()->email ?? 'Inicia sesión para continuar' }}</p></div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200">
        @foreach(['Direcciones','Métodos de pago','Notificaciones','Ayuda'] as $item)
            <button class="flex w-full items-center justify-between border-b border-slate-200 px-4 py-4 text-left last:border-b-0"><span>{{ $item }}</span><span>›</span></button>
        @endforeach
    </div>
</div>
