<div class="min-h-screen bg-gray-50 pb-24">
    <div class="mx-auto max-w-3xl">
        <div class="bg-white">
            <div class="relative aspect-square overflow-hidden bg-gray-100">
                @if ($product->image)
                    <img
                        src="/storage/{{ $product->image ?? asset('storage/'.$product->image_path) }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover"
                    >
                @else
                    <div class="flex h-full items-center justify-center text-gray-400">
                        Sin imagen
                    </div>
                @endif

                <a
                    href="{{ route('customer.home') }}"
                    wire:navigate
                    class="absolute left-4 top-4 rounded-full bg-white p-3 shadow"
                >
                    ←
                </a>
            </div>

            <div class="space-y-5 p-5">
                @if (session('success'))
                    <div class="rounded-xl bg-green-50 p-4 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        {{ $product->business?->name }}
                    </p>

                    <h1 class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $product->name }}
                    </h1>

                    <p class="mt-3 text-2xl font-bold text-gray-900">
                        ${{ number_format($product->price, 2) }}
                    </p>
                </div>

                @if ($product->description)
                    <div>
                        <h2 class="font-semibold text-gray-900">
                            Descripción
                        </h2>

                        <p class="mt-2 leading-7 text-gray-600">
                            {{ $product->description }}
                        </p>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <span class="font-semibold text-gray-900">
                        Cantidad
                    </span>

                    <div class="flex items-center gap-4 rounded-full border border-gray-200 px-3 py-2">
                        <button
                            type="button"
                            wire:click="decrementQuantity"
                            class="h-8 w-8 rounded-full bg-gray-100 text-xl"
                        >
                            −
                        </button>

                        <span class="min-w-6 text-center font-semibold">
                            {{ $quantity }}
                        </span>

                        <button
                            type="button"
                            wire:click="incrementQuantity"
                            class="h-8 w-8 rounded-full bg-gray-100 text-xl"
                        >
                            +
                        </button>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="addToCart"
                    class="w-full rounded-xl bg-gray-900 px-5 py-4 font-semibold text-dark"
                >
                    Agregar al carrito
                </button>
            </div>
        </div>
    </div>
</div>