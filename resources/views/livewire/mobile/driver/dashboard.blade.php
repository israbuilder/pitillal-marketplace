<div
    class="space-y-5"
    wire:poll.visible.5s="refreshAvailableOrders"
>
    <section class="flex items-center justify-between rounded-3xl bg-slate-900 p-5 text-white">
        <div>
            <p class="text-sm text-slate-400">
                Estado del conductor
            </p>

            <h2 class="mt-1 text-xl font-bold">
                {{ $online ? 'Disponible' : 'Desconectado' }}
            </h2>
        </div>

        <button
            type="button"
            wire:click="toggleOnline"
            wire:loading.attr="disabled"
            wire:target="toggleOnline"
            class="relative h-8 w-14 rounded-full transition
                {{ $online ? 'bg-emerald-500' : 'bg-slate-600' }}"
        >
            <span
                class="absolute top-1 h-6 w-6 rounded-full bg-white transition-all
                    {{ $online ? 'left-7' : 'left-1' }}"
            ></span>
        </button>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-lg font-bold">
                Solicitudes cercanas
            </h3>

            <div class="flex items-center gap-2">
                <span
                    wire:loading
                    wire:target="refreshAvailableOrders"
                    class="text-xs text-slate-400"
                >
                    Actualizando...
                </span>

                <span class="text-xs text-slate-500">
                    {{ $availableOrders->count() }}
                </span>
            </div>
        </div>
@if (session()->has('error'))
    <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif
        <div class="space-y-3">

            @forelse($availableOrders as $order)
                <article
                    wire:key="available-order-{{ $order->id }}"
                    class="rounded-3xl border border-slate-200 p-4 shadow-sm"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-slate-500">
                                Pedido #{{ $order->id }}
                            </p>

                            <h4 class="mt-1 text-lg font-bold">
                                ${{ number_format($order->total, 2) }}
                            </h4>
                        </div>

                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            Nuevo
                        </span>
                    </div>

                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <p>
                            <span class="font-semibold text-slate-900">
                                Recoger:
                            </span>

                            {{ $order->pickup_address ?? 'Comercio asignado' }}
                        </p>

                        <p>
                            <span class="font-semibold text-slate-900">
                                Entregar:
                            </span>

                            {{ $order->delivery_address }}
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="accept({{ $order->id }})"
                        {{-- wire:confirm="¿Aceptar este pedido?" --}}
                        wire:loading.attr="disabled"
                        wire:target="accept({{ $order->id }})"
                        class="mt-4 w-full rounded-2xl bg-indigo-600 px-4 py-3 font-bold text-white disabled:cursor-not-allowed disabled:opacity-60"
                    >
                   
                        <span wire:loading.remove wire:target="accept({{ $order->id }})">
                            Aceptar por
                            ${{ number_format($order->delivery_fee ?? 5, 2) }}
                        </span>

                        <span wire:loading wire:target="accept({{ $order->id }})">
                            Aceptando...
                        </span>
                    </button>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 p-10 text-center text-slate-500">
                    No hay pedidos disponibles en este momento.
                </div>
            @endforelse
        </div>
    </section>
</div>