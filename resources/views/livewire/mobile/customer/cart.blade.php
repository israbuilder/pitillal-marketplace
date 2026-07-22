<div class="space-y-4">
    @forelse($items as $key => $item)
        <article class="flex gap-3 rounded-2xl border border-slate-200 p-3 shadow-sm">
            <div class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-xl bg-slate-100">
                @if($item['image'])<img src="/storage/{{ $item['image'] }}" class="h-full w-full object-cover">@else 📦 @endif
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="truncate font-semibold">{{ $item['name'] }}</h3>
                <p class="mt-1 font-bold text-indigo-700">${{ number_format($item['price'], 2) }}</p>
                <div class="mt-2 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button wire:click="decrement('{{ $key }}')" class="grid h-8 w-8 place-items-center rounded-full bg-slate-100">−</button>
                        <span class="w-6 text-center font-semibold">{{ $item['quantity'] }}</span>
                        <button wire:click="increment('{{ $key }}')" class="grid h-8 w-8 place-items-center rounded-full bg-indigo-600 text-white">+</button>
                    </div>
                    <button wire:click="remove('{{ $key }}')" class="text-sm font-medium text-rose-600">Eliminar</button>
                </div>
            </div>
        </article>
    @empty
        <div class="rounded-3xl border border-dashed border-slate-300 p-10 text-center">
            <div class="text-5xl">🛒</div>
            <h2 class="mt-3 text-lg font-bold">Tu carrito está vacío</h2>
            <a href="{{ route('customer.home') }}" style="background: #E52471" class="mt-4 inline-block rounded px-3 py-3 font-semibold text-white">Explorar productos</a>
        </div>
    @endforelse

    @if(count($items))
        <div class="rounded-2xl bg-slate-100 p-4">
            <div class="flex justify-between text-sm"><span>Subtotal</span><span>${{ number_format($this->subtotal, 2) }}</span></div>
            <div class="mt-2 flex justify-between text-sm"><span>Envío</span><span>$5.00</span></div>
            <div class="mt-3 flex justify-between border-t border-slate-300 pt-3 text-lg font-bold"><span>Total</span><span>${{ number_format($this->subtotal + 5, 2) }}</span></div>
        </div>
        <a href="{{ route('customer.checkout') }}" style="background: #E52471" class="block w-full rounded-2xl  px-4 py-4 text-center font-bold text-white">Continuar</a>
    @endif
</div>
