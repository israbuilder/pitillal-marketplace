<div
    class="min-h-screen bg-slate-50 pb-28"
    wire:poll.15s
>
    {{-- <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur">
        <div class="mx-auto flex max-w-3xl items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">
                    Driver
                </p>

                <h1 class="text-xl font-black text-slate-900">
                    Órdenes
                </h1>
            </div>

            <a
                href="{{ route('driver.dashboard') }}"
                wire:navigate
                class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700"
            >
                Inicio
            </a>
        </div>
    </header> --}}

    <main class="mx-auto max-w-3xl space-y-5 px-4 py-5">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @error('order')
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-700">
                {{ $message }}
            </div>
        @enderror

        <div class="grid grid-cols-3 gap-2 rounded-3xl bg-white p-2 shadow-sm">
            <button
                type="button"
                wire:click="setTab('available')"
                class="rounded-2xl px-3 py-3 text-xs font-black transition
                    {{ $tab === 'available'
                        ? 'bg-indigo-600 text-white'
                        : 'text-slate-500 hover:bg-slate-100' }}"
            >
                Disponibles

                <span class="ml-1 rounded-full bg-white/20 px-2 py-0.5">
                    {{ $this->availableOrders->count() }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('active')"
                class="rounded-2xl px-3 py-3 text-xs font-black transition
                    {{ $tab === 'active'
                        ? 'bg-indigo-600 text-white'
                        : 'text-slate-500 hover:bg-slate-100' }}"
            >
                Activas

                <span class="ml-1 rounded-full bg-white/20 px-2 py-0.5">
                    {{ $this->activeOrders->count() }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('completed')"
                class="rounded-2xl px-3 py-3 text-xs font-black transition
                    {{ $tab === 'completed'
                        ? 'bg-indigo-600 text-white'
                        : 'text-slate-500 hover:bg-slate-100' }}"
            >
                Entregadas
            </button>
        </div>

        <div wire:loading.delay class="w-full">
            <div class="rounded-3xl bg-white p-5 text-center text-sm font-bold text-indigo-600 shadow-sm">
                Actualizando órdenes...
            </div>
        </div>

        <div wire:loading.remove>
            @php
                $orders = match ($tab) {
                    'available' => $this->availableOrders,
                    'active' => $this->activeOrders,
                    'completed' => $this->completedOrders,
                    default => collect(),
                };
            @endphp

            <div class="space-y-4">
                @forelse ($orders as $order)
                    <article
                        wire:key="driver-order-{{ $order->id }}"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm"
                    >
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">
                                        Orden #{{ $order->id }}
                                    </p>

                                    <h2 class="mt-1 truncate text-lg font-black text-slate-900">
                                        {{ $order->business?->name ?? 'Negocio' }}
                                    </h2>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $order->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <span
                                    class="shrink-0 rounded-full px-3 py-1 text-xs font-black
                                        @class([
                                            'bg-amber-100 text-amber-700' => in_array($order->status, ['pending', 'preparing']),
                                            'bg-blue-100 text-blue-700' => in_array($order->status, ['ready_for_pickup', 'accepted']),
                                            'bg-purple-100 text-purple-700' => $order->status === 'picked_up',
                                            'bg-emerald-100 text-emerald-700' => $order->status === 'delivered',
                                            'bg-slate-100 text-slate-700' => $order->status === 'cancelled',
                                        ])"
                                >
                                    {{ match ($order->status) {
                                        'pending' => 'Pendiente',
                                        'preparing' => 'Preparando',
                                        'ready_for_pickup' => 'Listo',
                                        'accepted' => 'Aceptado',
                                        'picked_up' => 'En camino',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado',
                                        default => ucfirst($order->status),
                                    } }}
                                </span>
                            </div>

                            <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Entregar en
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-700">
                                    {{ $order->delivery_address ?: 'Dirección no registrada' }}
                                </p>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-slate-400">
                                        Total
                                    </p>

                                    <p class="text-xl font-black text-slate-900">
                                        ${{ number_format((float) $order->total, 2) }}
                                    </p>
                                </div>


                               <a
                                    wire:navigate
                                    href="{{ route('driver.delivery', ['order' => $order->id]) }}"
                                    class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700"
                                >
                                    Delivery
                                </a>

                                <button
                                    type="button"
                                    wire:click="viewOrder({{ $order->id }})"
                                    class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700"
                                >
                                    Ver detalles
                                </button>
                            </div>

                            @if ($tab === 'available')
                                <button
                                    type="button"
                                    wire:click="acceptOrder({{ $order->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="acceptOrder({{ $order->id }})"
                                    class="mt-4 w-full rounded-2xl bg-indigo-600 py-4 text-sm font-black text-white shadow-lg disabled:opacity-50"
                                >
                                    Aceptar orden
                                </button>
                            @endif

                            @if ($tab === 'active')
                                <div class="mt-4 grid gap-2">
                                    @if (in_array($order->status, ['accepted', 'ready_for_pickup']))
                                        <button
                                            type="button"
                                            wire:click="markPickedUp({{ $order->id }})"
                                            class="w-full rounded-2xl bg-purple-600 py-4 text-sm font-black text-white"
                                        >
                                            Marcar como recogido
                                        </button>
                                    @endif

                                    @if ($order->status === 'picked_up')
                                        <button
                                            type="button"
                                            wire:click="markDelivered({{ $order->id }})"
                                            wire:confirm="¿Confirmas que entregaste este pedido?"
                                            class="w-full rounded-2xl bg-emerald-600 py-4 text-sm font-black text-white"
                                        >
                                            Confirmar entrega
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl bg-white px-6 py-14 text-center shadow-sm">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-3xl">
                            🚚
                        </div>

                        <h2 class="mt-4 text-lg font-black text-slate-900">
                            @if ($tab === 'available')
                                No hay órdenes disponibles
                            @elseif ($tab === 'active')
                                No tienes entregas activas
                            @else
                                No tienes entregas completadas
                            @endif
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">
                            Las órdenes nuevas aparecerán automáticamente.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    @if ($showDetails && $this->selectedOrder)
        @php
            $selectedOrder = $this->selectedOrder;
        @endphp

        <div
            class="fixed inset-0 z-50 flex items-end bg-slate-950/50"
            wire:key="selected-order-modal-{{ $selectedOrder->id }}"
        >
            <button
                type="button"
                wire:click="closeDetails"
                class="absolute inset-0"
                aria-label="Cerrar"
            ></button>

            <section style="z-index:100" class="relative max-h-[92vh] w-full overflow-y-auto rounded-t-[2xl] bg-white p-5 shadow-2xl">
                <div class="mx-auto max-w-3xl">
                    <div class="mx-auto mb-5 h-1.5 w-12 rounded-full bg-slate-200"></div>

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">
                                Orden #{{ $selectedOrder->id }}
                            </p>

                            <h2 class="mt-1 text-2xl font-black text-slate-900">
                                Detalles de entrega
                            </h2>
                        </div>

                        <button
                            type="button"
                            wire:click="closeDetails"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 font-black text-slate-600"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="mt-6 grid gap-4">
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Recoger en
                            </p>

                            <h3 class="mt-2 text-lg font-black text-slate-900">
                                {{ $selectedOrder->business?->name }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-600">
                                {{ $selectedOrder->business?->address }}
                            </p>

                            @if ($selectedOrder->business?->phone)
                                <a
                                    href="tel:{{ $selectedOrder->business->phone }}"
                                    class="mt-3 inline-flex rounded-xl bg-white px-3 py-2 text-sm font-bold text-indigo-600 shadow-sm"
                                >
                                    Llamar al negocio
                                </a>
                            @endif
                        </div>

                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Entregar a
                            </p>

                            <h3 class="mt-2 text-lg font-black text-slate-900">
                                {{ $selectedOrder->customer?->name ?? 'Cliente' }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-600">
                                {{ $selectedOrder->delivery_address }}
                            </p>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @if ($selectedOrder->customer?->phone)
                                    <a
                                        href="tel:{{ $selectedOrder->customer->phone }}"
                                        class="rounded-xl bg-white px-3 py-2 text-sm font-bold text-indigo-600 shadow-sm"
                                    >
                                        Llamar al cliente
                                    </a>
                                @endif

                                @if ($selectedOrder->delivery_lat && $selectedOrder->delivery_lng)
                                    <a
                                        href="https://www.google.com/maps/dir/?api=1&destination={{ $selectedOrder->delivery_lat }},{{ $selectedOrder->delivery_lng }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="rounded-xl bg-white px-3 py-2 text-sm font-bold text-indigo-600 shadow-sm"
                                    >
                                        Abrir mapa
                                    </a>
                                @elseif ($selectedOrder->delivery_address)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ urlencode($selectedOrder->delivery_address) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="rounded-xl bg-white px-3 py-2 text-sm font-bold text-indigo-600 shadow-sm"
                                    >
                                        Abrir mapa
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                            <h3 class="font-black text-slate-900">
                                Productos
                            </h3>

                            <div class="mt-4 divide-y divide-slate-100">
                                @foreach ($selectedOrder->items as $item)
                                    <div class="flex items-center justify-between gap-4 py-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-slate-800">
                                                {{ $item->product?->name ?? $item->name ?? 'Producto' }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                Cantidad: {{ $item->quantity }}
                                            </p>
                                        </div>

                                        <p class="shrink-0 text-sm font-black text-slate-700">
                                            ${{ number_format((float) ($item->line_total ?? 0), 2) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-3xl bg-slate-900 p-5 text-white">
                            <div class="flex justify-between text-sm text-slate-300">
                                <span>Subtotal</span>
                                <span>${{ number_format((float) $selectedOrder->subtotal, 2) }}</span>
                            </div>

                            <div class="mt-2 flex justify-between text-sm text-slate-300">
                                <span>Envío</span>
                                <span>${{ number_format((float) $selectedOrder->delivery_fee, 2) }}</span>
                            </div>

                            <div class="mt-4 flex justify-between border-t border-white/10 pt-4">
                                <span class="font-bold">Total</span>
                                <span class="text-xl font-black">
                                    ${{ number_format((float) $selectedOrder->total, 2) }}
                                </span>
                            </div>
                        </div>

                        @if ($selectedOrder->driver_id === null)
                            <button
                                type="button"
                                style="z-index:100"
                                wire:click="acceptOrder({{ $selectedOrder->id }})"
                                class="w-full rounded-2xl bg-indigo-600 py-4 font-black text-white"
                            >
                                Aceptar orden
                            </button>
                        @elseif ($selectedOrder->driver_id === auth()->id())
                            @if (in_array($selectedOrder->status, ['accepted', 'ready_for_pickup']))
                                <button
                                    type="button"
                                    wire:click="markPickedUp({{ $selectedOrder->id }})"
                                    class="w-full rounded-2xl bg-purple-600 py-4 font-black text-white"
                                >
                                    Marcar como recogido
                                </button>

                                <button
                                    type="button"
                                    wire:click="releaseOrder({{ $selectedOrder->id }})"
                                    wire:confirm="¿Quieres liberar esta orden?"
                                    class="w-full rounded-2xl bg-rose-50 py-4 font-black text-rose-600"
                                >
                                    Liberar orden
                                </button>
                            @elseif ($selectedOrder->status === 'picked_up')
                                <button
                                    type="button"
                                    wire:click="markDelivered({{ $selectedOrder->id }})"
                                    wire:confirm="¿Confirmas que entregaste este pedido?"
                                    class="w-full rounded-2xl bg-emerald-600 py-4 font-black text-white"
                                >
                                    Confirmar entrega
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </section>
        </div>
    @endif
</div>