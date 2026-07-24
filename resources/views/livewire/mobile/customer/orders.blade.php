<div class="min-h-screen bg-slate-50 pb-28">
        <div class="mx-auto max-w-md px-4 py-4">
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-xs text-slate-500">
                        Consulta el estado de tus compras
                    </p>
                </div>
            </div>

            <div class="relative mt-4">
               
                <input
                    type="search"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Número de pedido o negocio..."
                    class="h-12 w-full p-4 rounded-2xl border border-slate-200 bg-slate-100 pl-12 pr-4 text-sm outline-none focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100"
                >
            </div>
        </div>
  

    <main class="mx-auto max-w-md px-4 py-5">
        {{-- Filtros --}}
        <div class="-mx-4 overflow-x-auto px-4 pb-4">
            <div class="flex min-w-max gap-2 mb-5">
                @foreach ($statuses as $statusValue => $statusName)
                    <button
                        type="button"
                        wire:key="status-{{ $statusValue }}"
                        wire:click="selectStatus('{{ $statusValue }}')"
                        @class([
                            'rounded-full px-4 py-2 text-xs font-semibold transition',
                            'bg-slate-900 text-white' => $status === $statusValue,
                            'border border-slate-200 bg-white text-slate-600' => $status !== $statusValue,
                        ])
                    >
                        {{ $statusName }}
                    </button>
                @endforeach
            </div>
        </div>

        <div
            wire:loading.flex
            wire:target="search,status,selectStatus"
            class="items-center justify-center py-16"
        >
            <svg
                class="h-7 w-7 animate-spin text-orange-500"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"
                />
            </svg>
        </div>

        <div
            wire:loading.remove
            wire:target="search,status,selectStatus"
        >
            @if ($this->orders->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                        <svg
                            class="h-8 w-8 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.7"
                                d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Zm3 5h6M9 11h6M9 15h3"
                            />
                        </svg>
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-slate-900">
                        No hay pedidos
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        No encontramos pedidos que coincidan con los filtros seleccionados.
                    </p>

                    @if ($status !== 'all' || $search !== '')
                        <button
                            type="button"
                            wire:click="clearFilters"
                            class="mt-5 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white"
                        >
                            Limpiar filtros
                        </button>
                    @else
                        <a
                            href="{{ route('customer.home') }}"
                            wire:navigate
                            class="mt-5 inline-flex rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white"
                        >
                            Explorar productos
                        </a>
                    @endif
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($this->orders as $order)
                        <article
                            wire:key="order-{{ $order->id }}"
                            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                        >
                            <div class="border-b border-slate-100 px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-12 w-12 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
                                            @if ($order->business?->logo_url ?? $order->business?->logo_path)
                                            {{-- @dd($order->items->product) --}}
                                                <img style="width: 50px; object-fit: cover;"
                                                    src="{{ $order->business?->logo_url ?? asset('storage/'.$order->business?->logo_path) }}"
                                                    alt="{{ $order->business?->name }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <span class="m-auto font-bold text-slate-500">
                                                    {{ strtoupper(substr($order->business?->name ?? 'N', 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <h2 class="truncate text-sm font-bold text-slate-900">
                                                {{ $order->business?->name ?? 'Negocio' }}
                                            </h2>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Pedido
                                                #{{ $order->order_number ?? $order->id }}
                                            </p>
                                        </div>
                                    </div>

                                    <span
                                        class="shrink-0 rounded-full px-3 py-1 text-[10px] font-bold"
                                        @class([$this->statusClasses($order->status)])
                                    >
                                        {{ $this->statusLabel($order->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="px-4 py-4">
                                <div class="flex gap-2 overflow-hidden">
                                    @foreach ($order->items->take(4) as $item)
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-slate-100">
                                            @if ($item->product?->image ?? $item->product?->image_path)
                                                <img
                                                    src="/storage/{{ $item->product?->image ?? asset('/storage/'.$item->product?->image) }}"
                                                    alt="{{ $item->product_name ?? $item->product?->name }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <div class="flex h-full items-center justify-center">
                                                    <svg
                                                        class="h-6 w-6 text-slate-300"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="m4 16 4-4 4 4 3-3 5 5M4 5h16v14H4V5Z"
                                                        />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach

                                    @if ($order->items->count() > 4)
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-bold text-slate-600">
                                            +{{ $order->items->count() - 4 }}
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-3">
                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            Total
                                        </p>

                                        <p class="mt-1 text-base font-extrabold text-slate-900">
                                            ${{ number_format((float) $order->total, 2) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            Pago
                                        </p>

                                        <p class="mt-1 text-sm font-bold {{ $this->paymentStatusClasses($order->payment_status) }}">
                                            {{ $this->paymentStatusLabel($order->payment_status) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                                    <span>
                                        {{ $order->created_at?->format('d M Y, h:i A') }}
                                    </span>

                                    <span>
                                        {{ $order->items->sum('quantity') }}
                                        {{ $order->items->sum('quantity') === 1 ? 'producto' : 'productos' }}
                                    </span>
                                </div>

                                <div class="mt-4 flex gap-2">
                                    <a
                                        href="{{ route('customer.orders.show', $order) }}"
                                        wire:navigate
                                        class="flex h-11 flex-1 items-center justify-center rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700"
                                    >
                                        Ver detalles
                                    </a>

                                    @if ($this->canTrack($order))
                                        <a
                                            href="{{ route('customer.orders.show', $order) }}"
                                            wire:navigate
                                            class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-orange-500 text-sm font-semibold text-white"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 21s7-5.2 7-12A7 7 0 1 0 5 9c0 6.8 7 12 7 12Zm0-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                                />
                                            </svg>

                                            Seguir
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $this->orders->links() }}
                </div>
            @endif
        </div>
    </main>
</div>