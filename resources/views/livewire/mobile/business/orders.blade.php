<div class="space-y-4 pb-24">
    <div class="flex gap-2 overflow-x-auto">
        @foreach(['active' => 'Activos', 'pending' => 'Pendientes', 'ready' => 'Listos', 'delivered' => 'Entregados', 'all' => 'Todos'] as $value => $label)
            <button wire:click="$set('filter','{{ $value }}')" class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-bold {{ $filter === $value ? 'bg-slate-950 text-white' : 'bg-white text-slate-600' }}">{{ $label }}</button>
        @endforeach
    </div>
<div wire:poll.60s="refreshDashboard" class="mt2">
     @forelse($orders as $order)
        <article class="rounded-3xl bg-white p-5 shadow-sm">
            <div class="flex justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase text-indigo-600">{{ str_replace('_',' ', $order->status) }}</p>
                    <h2 class="mt-1 text-lg font-black">Pedido #{{ $order->id }}</h2>
                    <p class="text-sm text-slate-500">{{ $order->customer_name }} · {{ $order->customer_phone }}</p>
                </div>
                <p class="text-lg font-black">${{ number_format($order->total, 2) }}</p>
            </div>
            <p class="mt-3 text-sm text-slate-600">{{ $order->delivery_address }}</p>
            <p class="mt-2 text-xs font-bold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">Pago: {{ $order->payment_status }}</p>
            @if(in_array($order->status, ['pending','accepted']))
                <button wire:click="markReady({{ $order->id }})" class="mt-4 w-full rounded-2xl bg-emerald-500 py-3 font-black text-emerald-950">Pedido listo</button>
            @endif
        </article>
    @empty
        <p class="rounded-3xl bg-white p-5 text-sm text-slate-500">No hay pedidos en este filtro.</p>
    @endforelse
</div>
   
</div>
