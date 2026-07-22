<div class="space-y-5 pb-24">
    <section class="rounded-3xl bg-slate-950 p-5 text-white shadow-xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[.25em] text-slate-400">Panel del comercio</p>
                <h1 class="mt-2 text-2xl font-black">{{ $business->name }}</h1>
                <p class="mt-1 text-sm text-slate-300">{{ $business->address }}</p>
            </div>
            <button wire:click="toggleOpen" class="rounded-full px-4 py-2 text-xs font-bold {{ $business->is_open ? 'bg-emerald-400 text-emerald-950' : 'bg-rose-400 text-rose-950' }}">
                {{ $business->is_open ? 'Abierto' : 'Cerrado' }}
            </button>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-3">
        @foreach([
            ['label' => 'Pedidos activos', 'value' => $this->stats['pending']],
            ['label' => 'Pedidos hoy', 'value' => $this->stats['today']],
            ['label' => 'Ventas hoy', 'value' => '$'.number_format($this->stats['sales'], 2)],
            ['label' => 'Productos', 'value' => $this->stats['products']],
        ] as $card)
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-black text-slate-950">{{ $card['value'] }}</p>
            </article>
        @endforeach
    </section>

    <nav class="grid grid-cols-3 gap-2">
        <a href="{{ route('business.orders') }}" wire:navigate class="rounded-2xl bg-indigo-600 px-3 py-3 text-center text-sm font-bold text-white">Pedidos</a>
        <a href="{{ route('business.products') }}" wire:navigate class="rounded-2xl bg-white px-3 py-3 text-center text-sm font-bold text-slate-800 shadow-sm">Productos</a>
        <a href="{{ route('business.profile') }}" wire:navigate class="rounded-2xl bg-white px-3 py-3 text-center text-sm font-bold text-slate-800 shadow-sm">Perfil</a>
    </nav>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-black text-slate-950">Pedidos recientes</h2>
            <a href="{{ route('business.orders') }}" wire:navigate class="text-sm font-bold text-indigo-600">Ver todos</a>
        </div>
        <div wire:poll.30s="refreshDashboard" class="space-y-3">
            @forelse($orders as $order)
                <article class="rounded-3xl bg-white p-4 shadow-sm">
                    <div class="flex justify-between">
                        <div>
                            <p class="font-black">Pedido #{{ $order->id }}</p>
                            <p class="text-sm text-slate-500">{{ $order->customer_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black">${{ number_format($order->total, 2) }}</p>
                            <p class="text-xs font-bold uppercase text-indigo-600">{{ str_replace('_', ' ', $order->status) }}</p>
                        </div>
                    </div>
                </article>
            @empty
                <p class="rounded-3xl bg-white p-5 text-sm text-slate-500">Todavía no hay pedidos.</p>
            @endforelse
        </div>
    </section>
</div>
