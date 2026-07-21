<div class="space-y-5">
    <section class="rounded-3xl bg-indigo-600 p-5 text-white">
        <p class="text-sm text-indigo-100">Estado actual</p>
        <h2 class="mt-1 text-2xl font-bold">{{ ucfirst($order->status) }}</h2>
        <div class="mt-4 h-2 overflow-hidden rounded-full bg-indigo-400">
            @php($progress = ['awaiting_payment'=>10,'pending'=>20,'accepted'=>40,'picked_up'=>65,'on_the_way'=>85,'delivered'=>100][$order->status] ?? 20)
            <div class="h-full rounded-full bg-white" style="width: {{ $progress }}%">    @if(in_array($order->status, ['accepted','ready','picked_up','on_the_way']))
        <a href="{{ route('mobile.orders.tracking', $order) }}" wire:navigate class="block w-full rounded-2xl bg-slate-950 px-4 py-4 text-center font-black text-white">Ver repartidor en el mapa</a>
    @endif
</div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 p-4">
        <h3 class="font-bold">Resumen</h3>
        <div class="mt-3 space-y-3">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm"><span>{{ $item->quantity }} × {{ $item->product_name }}</span><span>${{ number_format($item->line_total, 2) }}</span></div>
            @endforeach
        </div>
        <div class="mt-4 flex justify-between border-t pt-4 text-lg font-bold"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
    </section>

    <section class="rounded-2xl bg-slate-100 p-4">
        <p class="text-xs uppercase tracking-wider text-slate-500">Entrega</p>
        <p class="mt-1 font-semibold">{{ $order->delivery_address }}</p>
        <p class="mt-2 text-sm text-slate-600">{{ $order->customer_phone }}</p>
    </section>
</div>
