<div class="space-y-5">
    <section class="rounded-3xl bg-linear-to-br from-indigo-600 to-violet-700 p-5 text-white shadow-lg">
        <p class="text-sm text-indigo-100">Entrega rápida en tu ciudad</p>
        <h2 class="mt-1 text-2xl font-bold">Compra local. Recibe hoy.</h2>
        <div class="mt-4 rounded-2xl bg-white p-2">
            <input wire:model.live.debounce.350ms="search" type="search" placeholder="Buscar productos..." class="w-full rounded-xl border-0 px-3 py-2 text-slate-900 outline-none ring-0">
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-lg font-bold">Productos destacados</h3>
            <span class="text-xs text-slate-500">{{ $products->count() }} resultados</span>
        </div>
        <div class="grid grid-cols-2 gap-3">
            @forelse($products as $product)
                <a href="{{ route('mobile.products.show', $product) }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition active:scale-[.98]">
                    <div class="aspect-square bg-slate-100">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="grid h-full place-items-center text-4xl">📦</div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h4 class="line-clamp-2 text-sm font-semibold">{{ $product->name }}</h4>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="font-bold text-indigo-700">${{ number_format($product->price, 2) }}</span>
                            <span class="rounded-full bg-indigo-50 px-2 py-1 text-xs text-indigo-700">Ver</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-2 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500">No se encontraron productos.</div>
            @endforelse
        </div>
    </section>
</div>
