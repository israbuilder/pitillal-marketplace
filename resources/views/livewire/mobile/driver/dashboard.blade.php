<div class="space-y-5">
    <section class="flex items-center justify-between rounded-3xl bg-slate-900 p-5 text-white">
        <div>
            <p class="text-sm text-slate-400">Estado del conductor</p>
            <h2 class="mt-1 text-xl font-bold">{{ $online ? 'Disponible' : 'Desconectado' }}</h2>
        </div>
        <button wire:click="toggleOnline" class="relative h-8 w-14 rounded-full {{ $online ? 'bg-emerald-500' : 'bg-slate-600' }}">
            <span class="absolute top-1 h-6 w-6 rounded-full bg-white transition {{ $online ? 'left-7' : 'left-1' }}"></span>
        </button>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-lg font-bold">Solicitudes cercanas</h3>
            <span class="text-xs text-slate-500">{{ $availableOrders->count() }}</span>
        </div>

        <div class="space-y-3">
            @forelse($availableOrders as $order)
                <article class="rounded-3xl border border-slate-200 p-4 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Pedido #{{ $order->id }}</p>
                            <h4 class="mt-1 text-lg font-bold">${{ number_format($order->total, 2) }}</h4>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Nuevo</span>
                    </div>
                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-900">Recoger:</span> {{ $order->pickup_address ?? 'Comercio asignado' }}</p>
                        <p><span class="font-semibold text-slate-900">Entregar:</span> {{ $order->delivery_address }}</p>
                    </div>
                    <button wire:click="accept({{ $order->id }})" wire:confirm="¿Aceptar este pedido?" class="mt-4 w-full rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white">Aceptar por ${{ number_format($order->driver_earning ?? 5, 2) }}</button>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 p-10 text-center text-slate-500">No hay pedidos disponibles en este momento.</div>
            @endforelse
        </div>
    </section>
</div>
