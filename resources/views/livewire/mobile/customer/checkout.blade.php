<div class="min-h-screen bg-slate-50 pb-32">
        <div class="mx-auto flex max-w-md items-center gap-3 px-4">
            <div>
                <h2 class="text-slate-500">
                    Confirma tu entrega y pago
                </h2>
            </div>
        </div>

    <main class="mx-auto max-w-md space-y-5 px-4 py-5">
        @error('checkout')
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $message }}
            </div>
        @enderror

        @error('cart')
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $message }}
            </div>
        @enderror

        {{-- Productos --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-bold text-slate-900">
                    Tu pedido
                </h2>

                <span class="text-xs font-semibold text-slate-500">
                    {{ collect($items)->sum('quantity') }} productos
                </span>
            </div>

            <div class="space-y-4">
                @foreach ($items as $item)
                    <div
                        wire:key="checkout-item-{{ $item['product_id'] }}"
                        class="flex gap-3"
                    >
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
                            @if (! empty($item['image']))
                                <img
                                    src="{{ asset('storage/'.$item['image']) }}"
                                    alt="{{ $item['name'] }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-slate-300">
                                    <svg
                                        class="h-7 w-7"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M4 5h16v14H4V5Zm2 11 4-4 3 3 2-2 3 3"
                                        />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900">
                                {{ $item['name'] }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Cantidad: {{ $item['quantity'] }}
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-900">
                                ${{ number_format(
                                    (float) $item['price'] *
                                    (int) $item['quantity'],
                                    2
                                ) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Información personal --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="mb-4 font-bold text-slate-900">
                Información de contacto
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Nombre completo
                    </label>

                    <input
                        type="text"
                        wire:model.blur="customerName"
                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >

                    @error('customerName')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Teléfono
                    </label>

                    <input
                        type="tel"
                        wire:model.blur="phone"
                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >

                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        wire:model.blur="email"
                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >

                    @error('email')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Dirección --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="mb-4 font-bold text-slate-900">
                Dirección de entrega
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Dirección
                    </label>

                    <input
                        type="text"
                        wire:model.blur="address"
                        placeholder="Calle y número"
                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >

                    @error('address')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Apartamento o unidad
                    </label>

                    <input
                        type="text"
                        wire:model.blur="apartment"
                        placeholder="Opcional"
                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Ciudad
                    </label>

                    <input
                        type="text"
                        wire:model.blur="city"
                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >

                    @error('city')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Estado
                        </label>

                        <input
                            type="text"
                            wire:model.blur="state"
                            maxlength="2"
                            class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm uppercase outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >

                        @error('state')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Código postal
                        </label>

                        <input
                            type="text"
                            wire:model.blur="zipCode"
                            class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >

                        @error('zipCode')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Instrucciones de entrega
                    </label>

                    <textarea
                        wire:model.blur="deliveryInstructions"
                        rows="3"
                        placeholder="Puerta, código, referencias..."
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    ></textarea>
                </div>
            </div>
        </section>

        {{-- Método de pago --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="mb-4 font-bold text-slate-900">
                Método de pago
            </h2>

            <div class="space-y-3">
                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 p-4">
                    <input
                        type="radio"
                        wire:model="paymentMethod"
                        value="cash_on_delivery"
                        class="text-orange-500 focus:ring-orange-400"
                    >

                    <div>
                        <p class="text-sm font-bold text-slate-900">
                            Pago al entregar
                        </p>

                        <p class="text-xs text-slate-500">
                            Paga cuando recibas tu pedido
                        </p>
                    </div>
                </label>

                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 p-4">
                    <input
                        type="radio"
                        wire:model="paymentMethod"
                        value="cash"
                        class="text-orange-500 focus:ring-orange-400"
                    >

                    <div>
                        <p class="text-sm font-bold text-slate-900">
                            Efectivo
                        </p>

                        <p class="text-xs text-slate-500">
                            El conductor recibirá el pago
                        </p>
                    </div>
                </label>

                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 p-4">
                    <input
                        type="radio"
                        wire:model="paymentMethod"
                        value="card"
                        class="text-orange-500 focus:ring-orange-400"
                    >

                    <div>
                        <p class="text-sm font-bold text-slate-900">
                            Tarjeta
                        </p>

                        <p class="text-xs text-slate-500">
                            La integración de pago se agregará después
                        </p>
                    </div>
                </label>
            </div>
        </section>

        {{-- Resumen --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="mb-4 font-bold text-slate-900">
                Resumen
            </h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="flex justify-between text-slate-600">
                    <span>Entrega</span>
                    <span>${{ number_format($deliveryFee, 2) }}</span>
                </div>

                <div class="flex justify-between text-slate-600">
                    <span>Impuestos</span>
                    <span>${{ number_format($tax, 2) }}</span>
                </div>

                <div class="border-t border-slate-200 pt-3">
                    <div class="flex justify-between text-lg font-extrabold text-slate-900">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div>
                 <button
                type="button"
                style="margin:20px"
                wire:click="placeOrder"
                wire:loading.attr="disabled"
                wire:target="placeOrder"
                class="flex h-14 w-full items-center justify-center rounded-2xl bg-black px-5 text-base font-bold text-dark shadow-lg"
            >
                <span
                    wire:loading.remove
                    wire:target="placeOrder"
                >
                    Confirmar pedido · ${{ number_format($total, 2) }}
                </span>

                <span
                    wire:loading.flex
                    wire:target="placeOrder"
                    class="items-center gap-2"
                >
                    <svg
                        class="h-5 w-5 animate-spin"
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

                    Procesando...
                </span>
            </button>
            </div>
        </section>
    </main>

    <div class="fixed bottom-2 left-0 right-0 z-40 border-t border-slate-200 bg-white p-4">
        <div class="mx-auto max-w-md">
           
        </div>
    </div>
</div>