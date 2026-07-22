<div class="min-h-screen bg-slate-50 pb-28">
   
        <div class="mx-auto max-w-md px-4 py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                        Bienvenido
                    </p>

                    <h1 class="truncate text-xl font-bold text-slate-900">
                        {{ auth()->user()->name }}
                    </h1>
                </div>

              

                 <div class="flex items-center gap-2">

        <!-- Carrito -->
        <a
                    href="{{ route('customer.cart') }}"
                    wire:navigate
                    class="relative flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-700"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 4h14M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm8 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                        />
                    </svg>
 
                    @if ($this->cartCount() > 0)
                        <span
                            class="absolute -right-1 -top-1 flex min-h-5 min-w-5 items-center justify-center rounded-full bg-orange-500 px-1 text-[10px] font-bold text-white"
                        >
                            {{ $this->cartCount() }}
                        </span>
                    @endif
                </a>

     {{-- <!-- Perfil --> <div class="relative" x-data="{ open: false }"> 
        <button @click="open = !open" class="flex h-11 w-11 items-center justify-center rounded-full bg-orange-500 text-dark font-semibold" > {{ strtoupper(substr(auth()->user()->name,0,4)) }} </button> 
        <div x-show="open" x-transition @click.outside="open = false" class="absolute right-0 top-12 z-50 w-56 rounded-xl border bg-white shadow-xl" > <a href="{{ route('customer.orders') }}" wire:navigate class="block px-4 py-3 hover:bg-gray-50" > Mis pedidos </a> <a href="{{ route('customer.cart') }}" wire:navigate class="block px-4 py-3 hover:bg-gray-50" > Mi carrito </a> <hr> <form method="POST" action="{{ route('logout') }}" > @csrf <button class="w-full px-4 py-3 text-left text-red-600 hover:bg-red-50" > Cerrar sesión </button> </form> </div> </div> --}}

    </div>
</div>
            

         <div class="relative mt-4">
    <div
        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
    >
       
    </div>

    <input
        type="search"
        wire:model.live.debounce.400ms="search"
        placeholder="Buscar productos..."
        class="block h-12 w-full rounded-2xl border border-slate-200 bg-slate-100 py-3 pl-4 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100"
    >
</div>
        </div>

   

    <main class="mx-auto max-w-md space-y-6 px-4 py-5">
        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 2500)"
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
            >
                {{ session('success') }}
            </div>
        @endif

        {{-- Banner --}}
        <section class="overflow-hidden rounded-3xl bg-slate-950 p-5 text-white shadow-lg">
            <div class="flex items-center gap-4">
                <div class="min-w-0 flex-1">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">
                        Entrega local
                    </span>

                    <h2 class="mt-3 text-2xl font-bold leading-tight">
                        Compra cerca y recibe rápido
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Descubre productos de negocios locales disponibles cerca de ti.
                    </p>
                </div>

                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-orange-500">
                    <svg
                        class="h-12 w-12"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3 7h11v10H3V7Zm11 3h3l4 4v3h-7v-7ZM7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"
                        />
                    </svg>
                </div>
            </div>
        </section>

        {{-- Negocios --}}
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">
                    Negocios
                </h2>

                @if ($selectedBusinessId)
                    <button
                        type="button"
                        wire:click="selectBusiness"
                        class="text-sm font-semibold text-orange-600"
                    >
                        Ver todos
                    </button>
                @endif
            </div>

            <div class="-mx-4 overflow-x-auto px-4 pb-2">
                <div class="flex min-w-max gap-3">
                    <button
                        type="button"
                        wire:click="selectBusiness"
                        @class([
                            'flex w-20 flex-col items-center gap-2 rounded-2xl border p-3 transition',
                            'border-orange-500 bg-orange-50' => $selectedBusinessId === null,
                            'border-slate-200 bg-white' => $selectedBusinessId !== null,
                        ])
                    >
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-900 text-white">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M4 7h16M5 7l1-4h12l1 4M5 7v13h14V7M9 11h6"
                                />
                            </svg>
                        </span>

                        <span class="text-xs font-semibold text-slate-700">
                            Todos
                        </span>
                    </button>

                    @foreach ($this->businesses as $business)
                        <button
                            type="button"
                            wire:key="business-{{ $business->id }}"
                            wire:click="selectBusiness({{ $business->id }})"
                            @class([
                                'flex w-24 flex-col items-center gap-2 rounded-2xl border p-3 transition',
                                'border-orange-500 bg-orange-50' => $selectedBusinessId === $business->id,
                                'border-slate-200 bg-white' => $selectedBusinessId !== $business->id,
                            ])
                        >
                            <span class="flex h-11 w-11 overflow-hidden rounded-full bg-slate-100">
                                @if ($business->logo_url ?? $business->logo_path)
                                    <img
                                        src="{{ $business->logo_url ?? asset('storage/'.$business->logo_path) }}"
                                        alt="{{ $business->name }}"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <span class="m-auto text-lg font-bold text-slate-500">
                                        {{ strtoupper(substr($business->name, 0, 1)) }}
                                    </span>
                                @endif
                            </span>

                            <span class="line-clamp-1 w-full text-xs font-semibold text-slate-700">
                                {{ $business->name }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Encabezado productos --}}
        <section>
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Productos
                    </h2>

                    <p class="text-xs text-slate-500">
                        {{ $this->products->total() }} resultados
                    </p>
                </div>

                <select
                    wire:model.live="sort"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                >
                    <option value="latest">Más recientes</option>
                    <option value="price_low">Menor precio</option>
                    <option value="price_high">Mayor precio</option>
                    <option value="name">Nombre</option>
                </select>
            </div>

            <div
                wire:loading.flex
                wire:target="search,selectedBusinessId,sort"
                class="mb-4 items-center justify-center rounded-2xl bg-white py-6"
            >
                <svg
                    class="h-6 w-6 animate-spin text-orange-500"
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
                wire:target="search,selectedBusinessId,sort"
            >
                @if ($this->products->isEmpty())
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                            <svg
                                class="h-7 w-7 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                                />
                            </svg>
                        </div>

                        <h3 class="mt-4 font-bold text-slate-900">
                            No encontramos productos
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Prueba otro nombre o elimina los filtros.
                        </p>

                        <button
                            type="button"
                            wire:click="clearFilters"
                            class="mt-5 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($this->products as $product)
                            <article
                                wire:key="product-{{ $product->id }}"
                                class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                            >
                                <a
                                    href="{{ route('customer.products.show', $product) }}"
                                    wire:navigate
                                    class="block"
                                >
                                    <div class="aspect-square overflow-hidden bg-slate-100">
                                        @if ($product->image ?? $product->image_path)
                                            <img
                                                src="{{asset('storage/'. $product->image)}}"
                                                alt="{{ $product->name }}"
                                                class="h-full w-full object-cover transition duration-300 hover:scale-105"
                                            >
                                        @else
                                            <div class="flex h-full items-center justify-center">
                                                <svg
                                                    class="h-12 w-12 text-slate-300"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="m4 16 4-4 4 4 3-3 5 5M4 5h16v14H4V5Zm11 4h.01"
                                                    />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </a>

                                <div class="p-3">
                                    <p class="truncate text-[11px] font-medium text-slate-500">
                                        {{ $product->business?->name }}
                                    </p>

                                    <a
                                        href="{{ route('customer.orders.show', $product) }}"
                                        wire:navigate
                                        class="mt-1 line-clamp-2 min-h-10 text-sm font-bold leading-5 text-slate-900"
                                    >
                                        {{ $product->name }}
                                    </a>

                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <span class="text-base font-extrabold text-slate-900">
                                            ${{ number_format((float) $product->price, 2) }}
                                        </span>

                                        <button
                                            type="button"
                                            wire:click="addToCart({{ $product->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="addToCart({{ $product->id }})"
                                            class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-500 text-white shadow-sm disabled:opacity-50"
                                        >
                                            <svg
                                                wire:loading.remove
                                                wire:target="addToCart({{ $product->id }})"
                                                class="h-5 w-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 5v14m-7-7h14"
                                                />
                                            </svg>

                                            <svg
                                                wire:loading
                                                wire:target="addToCart({{ $product->id }})"
                                                class="h-4 w-4 animate-spin"
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
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $this->products->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>
</div>