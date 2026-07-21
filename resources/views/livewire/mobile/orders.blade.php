<div class="space-y-3">
    @forelse($orders as $order)
        <a href="{{ route('mobile.orders.show', $order) }}" class="block rounded-2xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div><p class="text-xs text-slate-500">Pedido #{{ $order->id }}</p><h3 class="font-bold">${{ number_format($order->total, 2) }}</h3></div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ ucfirst($order->status) }}</span>
            </div>
            <p class="mt-3 text-sm text-slate-600">{{ $order->delivery_address }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
        </a>
    @empty
        <div class="rounded-3xl border border-dashed border-slate-300 p-10 text-center text-slate-500">Todavía no tienes pedidos.</div>
    @endforelse
    {{ $orders->links() }}
</div>
