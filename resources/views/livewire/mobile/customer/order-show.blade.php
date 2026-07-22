<div
    class="min-h-screen bg-slate-50 pb-28"
    wire:poll.5s="refreshOrder"
>
    @php
        $statusConfig = [
            'awaiting_payment' => [
                'label' => 'Esperando pago',
                'description' => 'Completa el pago para continuar con tu pedido.',
                'class' => 'bg-amber-100 text-amber-700',
                'progress' => 5,
            ],

            'pending' => [
                'label' => 'Buscando conductor',
                'description' => 'Tu pedido está disponible para conductores cercanos.',
                'class' => 'bg-blue-100 text-blue-700',
                'progress' => 20,
            ],

            'ready_for_driver' => [
                'label' => 'Buscando conductor',
                'description' => 'Estamos buscando un conductor para tu pedido.',
                'class' => 'bg-blue-100 text-blue-700',
                'progress' => 20,
            ],

            'driver_assigned' => [
                'label' => 'Conductor asignado',
                'description' => 'Un conductor aceptó tu pedido.',
                'class' => 'bg-indigo-100 text-indigo-700',
                'progress' => 45,
            ],

            'picked_up' => [
                'label' => 'Pedido recogido',
                'description' => 'El conductor ya recogió tu pedido.',
                'class' => 'bg-purple-100 text-purple-700',
                'progress' => 65,
            ],

            'out_for_delivery' => [
                'label' => 'En camino',
                'description' => 'Tu pedido está en camino.',
                'class' => 'bg-orange-100 text-orange-700',
                'progress' => 85,
            ],

            'delivered' => [
                'label' => 'Entregado',
                'description' => 'Tu pedido fue entregado correctamente.',
                'class' => 'bg-emerald-100 text-emerald-700',
                'progress' => 100,
            ],

            'cancelled' => [
                'label' => 'Cancelado',
                'description' => 'Este pedido fue cancelado.',
                'class' => 'bg-red-100 text-red-700',
                'progress' => 0,
            ],
        ];

        $currentStatus = $statusConfig[$order->status] ?? [
            'label' => ucfirst(str_replace('_', ' ', $order->status)),
            'description' => 'El estado del pedido se está actualizando.',
            'class' => 'bg-slate-100 text-slate-700',
            'progress' => 10,
        ];

        $paymentConfig = [
            'pending' => [
                'label' => 'Pago pendiente',
                'class' => 'bg-amber-100 text-amber-700',
            ],

            'cash_pending' => [
                'label' => 'Pago en efectivo',
                'class' => 'bg-blue-100 text-blue-700',
            ],

            'paid' => [
                'label' => 'Pagado',
                'class' => 'bg-emerald-100 text-emerald-700',
            ],

            'failed' => [
                'label' => 'Pago fallido',
                'class' => 'bg-red-100 text-red-700',
            ],

            'refunded' => [
                'label' => 'Reembolsado',
                'class' => 'bg-purple-100 text-purple-700',
            ],

            'expired' => [
                'label' => 'Pago expirado',
                'class' => 'bg-slate-100 text-slate-700',
            ],
        ];

        $currentPayment = $paymentConfig[$order->payment_status] ?? [
            'label' => ucfirst(str_replace('_', ' ', $order->payment_status)),
            'class' => 'bg-slate-100 text-slate-700',
        ];

        $statusSteps = [
            [
                'status' => 'pending',
                'label' => 'Pedido creado',
                'description' => 'Recibimos tu pedido.',
                'completed' => ! in_array($order->status, [
                    'awaiting_payment',
                    'cancelled',
                ], true),
            ],

            [
                'status' => 'driver_assigned',
                'label' => 'Conductor asignado',
                'description' => 'Un conductor aceptó el pedido.',
                'completed' => in_array($order->status, [
                    'driver_assigned',
                    'picked_up',
                    'out_for_delivery',
                    'delivered',
                ], true),
            ],

            [
                'status' => 'picked_up',
                'label' => 'Pedido recogido',
                'description' => 'El conductor recogió los productos.',
                'completed' => in_array($order->status, [
                    'picked_up',
                    'out_for_delivery',
                    'delivered',
                ], true),
            ],

            [
                'status' => 'out_for_delivery',
                'label' => 'En camino',
                'description' => 'El conductor va hacia tu dirección.',
                'completed' => in_array($order->status, [
                    'out_for_delivery',
                    'delivered',
                ], true),
            ],

            [
                'status' => 'delivered',
                'label' => 'Entregado',
                'description' => 'El pedido llegó a su destino.',
                'completed' => $order->status === 'delivered',
            ],
        ];

        $showTrackingMap = in_array($order->status, [
            'driver_assigned',
            'picked_up',
            'out_for_delivery',
        ], true);

        $driverLatitude = $order->driver_latitude ?? null;
        $driverLongitude = $order->driver_longitude ?? null;

        $deliveryLatitude = $order->delivery_latitude ?? null;
        $deliveryLongitude = $order->delivery_longitude ?? null;
    @endphp

    {{-- Encabezado --}}
        <div class="mx-auto flex max-w-md items-center gap-3 px-4 py-4">
           

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-lg font-extrabold text-slate-900">
                    Seguimiento del pedido
                </h1>

                <p class="truncate text-xs text-slate-500">
                    {{ $order->order_number }}
                </p>
            </div>

            <button
                type="button"
                wire:click="refreshOrder"
                wire:loading.attr="disabled"
                wire:target="refreshOrder"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700 transition hover:bg-slate-200 disabled:opacity-60"
                aria-label="Actualizar pedido"
            >
                <svg
                    wire:loading.class="animate-spin"
                    wire:target="refreshOrder"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4m-4 4a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"
                    />
                </svg>
            </button>
        </div>
    

    <main class="mx-auto max-w-md space-y-5 px-4 py-5">
        @if (session()->has('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @error('order')
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ $message }}
            </div>
        @enderror

        {{-- Estado principal --}}
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Estado actual
                        </p>

                        <h2 class="mt-1 text-xl font-extrabold text-slate-900">
                            {{ $currentStatus['label'] }}
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            {{ $currentStatus['description'] }}
                        </p>
                    </div>

                    <span class="shrink-0 rounded-full px-3 py-1.5 text-xs font-bold {{ $currentStatus['class'] }}">
                        {{ $currentStatus['label'] }}
                    </span>
                </div>

                @if ($order->status !== 'cancelled')
                    <div class="mt-5">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">
                                Progreso
                            </span>

                            <span class="text-xs font-bold text-slate-700">
                                {{ $currentStatus['progress'] }}%
                            </span>
                        </div>

                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                            <div
                                class="h-full rounded-full bg-orange-500 transition-all duration-700"
                                style="width: {{ $currentStatus['progress'] }}%"
                            ></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-5 py-3">
                <div class="flex items-center justify-between gap-3 text-xs">
                    <span class="text-slate-500">
                        Última actualización
                    </span>

                    <span class="font-semibold text-slate-700">
                        {{ $order->updated_at?->diffForHumans() }}
                    </span>
                </div>
            </div>
        </section>

        {{-- Pago --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Pago
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-900">
                        @switch($order->payment_method)
                            @case('cash')
                            @case('cash_on_delivery')
                                Efectivo al entregar
                                @break

                            @case('stripe')
                            @case('card')
                                Tarjeta
                                @break

                            @default
                                {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                        @endswitch
                    </p>
                </div>

                <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $currentPayment['class'] }}">
                    {{ $currentPayment['label'] }}
                </span>
            </div>

            @if (
                $order->payment_method === 'stripe' &&
                in_array($order->payment_status, ['pending', 'failed', 'expired'], true)
            )
                <a
                    href="{{ route('customer.orders.payment', $order) }}"
                    wire:navigate
                    class="mt-4 flex h-12 w-full items-center justify-center rounded-2xl bg-orange-500 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-orange-600"
                >
                    Continuar con el pago
                </a>
            @endif

            @if (
                in_array($order->payment_method, ['cash', 'cash_on_delivery'], true) &&
                $order->payment_status === 'cash_pending'
            )
                <div class="mt-4 rounded-2xl bg-blue-50 p-4 text-sm leading-6 text-blue-700">
                    Ten preparado el efectivo. El conductor marcará el pedido como pagado cuando reciba el dinero.
                </div>
            @endif
        </section>

        {{-- Mapa --}}
        @if ($showTrackingMap)
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 px-5 py-4">
                    <div>
                        <h2 class="font-extrabold text-slate-900">
                            Seguimiento en tiempo real
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Ubicación actual del conductor
                        </p>
                    </div>

                    @if ($order->estimated_arrival_at)
                        <div class="text-right">
                            <p class="text-xs text-slate-400">
                                Llegada estimada
                            </p>

                            <p class="text-sm font-extrabold text-orange-600">
                                {{ $order->estimated_arrival_at->format('g:i A') }}
                            </p>
                        </div>
                    @endif
                </div>

                @if ($driverLatitude && $driverLongitude)
                    <div
                        id="delivery-map"
                        wire:ignore
                        class="h-80 w-full bg-slate-200"
                        data-driver-latitude="{{ $driverLatitude }}"
                        data-driver-longitude="{{ $driverLongitude }}"
                        data-delivery-latitude="{{ $deliveryLatitude }}"
                        data-delivery-longitude="{{ $deliveryLongitude }}"
                    ></div>

                    <div class="border-t border-slate-100 px-5 py-3">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-500"></span>
                            Ubicación actualizada automáticamente
                        </div>
                    </div>
                @else
                    <div class="flex h-72 flex-col items-center justify-center bg-slate-100 px-6 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                            <svg
                                class="h-8 w-8"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"
                                />

                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2"
                                    stroke-width="1.8"
                                />
                            </svg>
                        </div>

                        <h3 class="mt-4 font-bold text-slate-900">
                            Esperando ubicación
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            El mapa aparecerá cuando el conductor comparta su ubicación.
                        </p>
                    </div>
                @endif
            </section>
        @elseif (
            in_array($order->status, ['pending', 'ready_for_driver'], true)
        )
            <section class="rounded-3xl border border-blue-200 bg-blue-50 p-6 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-blue-500 shadow-sm">
                    <svg
                        class="h-8 w-8 animate-pulse"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M3 11h18M5 11l2-5h10l2 5M5 11v7h2m10 0h2v-7M8 18h8M7 14h.01M17 14h.01"
                        />
                    </svg>
                </div>

                <h2 class="mt-4 text-lg font-extrabold text-slate-900">
                    Buscando conductor
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Tu pedido está disponible para conductores cercanos. Esta pantalla se actualizará automáticamente.
                </p>
            </section>
        @endif

        {{-- Conductor --}}
        @if ($order->driver)
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-orange-100 text-xl font-extrabold text-orange-600">
                        @if ($order->driver->avatar)
                            <img
                                src="{{ asset('storage/'.$order->driver->avatar) }}"
                                alt="{{ $order->driver->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ strtoupper(substr($order->driver->name, 0, 1)) }}
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Tu conductor
                        </p>

                        <h2 class="truncate text-lg font-extrabold text-slate-900">
                            {{ $order->driver->name }}
                        </h2>

                        @if ($order->driver->vehicle_type || $order->driver->vehicle_plate)
                            <p class="mt-1 truncate text-sm text-slate-500">
                                {{ $order->driver->vehicle_type }}

                                @if ($order->driver->vehicle_plate)
                                    · {{ $order->driver->vehicle_plate }}
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    @if ($order->driver->phone)
                        <a
                            href="tel:{{ $order->driver->phone }}"
                            class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-emerald-500 px-4 text-sm font-bold text-white"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 5a2 2 0 0 1 2-2h3l2 5-2 1a15 15 0 0 0 7 7l1-2 5 2v3a2 2 0 0 1-2 2C10.2 21 3 13.8 3 5Z"
                                />
                            </svg>

                            Llamar
                        </a>
                    @endif

                    @if ($order->driver->phone)
                        <a
                            href="sms:{{ $order->driver->phone }}"
                            class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-blue-500 px-4 text-sm font-bold text-white"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 10h8M8 14h5m8-2a9 9 0 1 1-4-7.5L21 4v8Z"
                                />
                            </svg>

                            Mensaje
                        </a>
                    @endif
                </div>
            </section>
        @endif

        {{-- Línea de progreso --}}
        @if ($order->status !== 'cancelled')
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-extrabold text-slate-900">
                    Progreso del pedido
                </h2>

                <div class="mt-5">
                    @foreach ($statusSteps as $index => $step)
                        <div class="relative flex gap-4">
                            @if (! $loop->last)
                                <div
                                    class="absolute left-[15px] top-8 h-[calc(100%-10px)] w-0.5 {{ $step['completed'] ? 'bg-emerald-500' : 'bg-slate-200' }}"
                                ></div>
                            @endif

                            <div
                                class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 {{ $step['completed'] ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-200 bg-white text-slate-300' }}"
                            >
                                @if ($step['completed'])
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="m5 13 4 4L19 7"
                                        />
                                    </svg>
                                @else
                                    <span class="text-xs font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                @endif
                            </div>

                            <div class="{{ $loop->last ? '' : 'pb-7' }}">
                                <p class="text-sm font-bold {{ $step['completed'] ? 'text-slate-900' : 'text-slate-400' }}">
                                    {{ $step['label'] }}
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    {{ $step['description'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Información del negocio --}}
        @if ($order->business)
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-100">
                        @if ($order->business->logo)
                            <img
                                src="{{ asset('storage/'.$order->business->logo) }}"
                                alt="{{ $order->business->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
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
                                    d="M4 10h16M5 10V7l2-3h10l2 3v3M6 10v10h12V10M9 20v-6h6v6"
                                />
                            </svg>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Negocio
                        </p>

                        <h2 class="truncate font-extrabold text-slate-900">
                            {{ $order->business->name }}
                        </h2>

                        @if ($order->business->address)
                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                {{ $order->business->address }}
                            </p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        {{-- Dirección de entrega --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"
                        />

                        <circle
                            cx="12"
                            cy="10"
                            r="2"
                            stroke-width="2"
                        />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Dirección de entrega
                    </p>

                    <p class="mt-1 text-sm font-bold leading-6 text-slate-900">
                        {{ $order->delivery_address }}
                    </p>

                    @if ($order->user)
                        @if (
                            $order->user->city ||
                            $order->user->state ||
                            $order->user->zip_code
                        )
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $order->user->city }}

                                @if ($order->user->city && $order->user->state)
                                    ,
                                @endif

                                {{ $order->user->state }}
                                {{ $order->user->zip_code }}
                            </p>
                        @endif
                    @endif

                    @if ($order->notes)
                        <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                            <p class="text-xs font-bold text-slate-500">
                                Instrucciones
                            </p>

                            <p class="mt-1 text-sm leading-6 text-slate-700">
                                {{ $order->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Productos --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-extrabold text-slate-900">
                    Productos
                </h2>

                <span class="text-xs font-semibold text-slate-500">
                    {{ $order->items->sum('quantity') }} unidades
                </span>
            </div>

            <div class="mt-4 divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    <article
                        wire:key="order-item-{{ $item->id }}"
                        class="flex gap-3 py-4 first:pt-0 last:pb-0"
                    >
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-100">
                            @if ($item->product?->image)
                                <img
                                    src="{{ asset('storage/'.$item->product->image) }}"
                                    alt="{{ $item->product_name }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <svg
                                    class="h-7 w-7 text-slate-300"
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
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900">
                                {{ $item->product_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $item->quantity }}
                                ×
                                ${{ number_format((float) $item->unit_price, 2) }}
                            </p>

                            <p class="mt-2 text-sm font-extrabold text-slate-900">
                                ${{ number_format((float) $item->line_total, 2) }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Totales --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-900">
                Resumen
            </h2>

            <div class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between text-slate-600">
                    <span>Subtotal</span>

                    <span>
                        ${{ number_format((float) $order->subtotal, 2) }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-slate-600">
                    <span>Entrega</span>

                    <span>
                        ${{ number_format((float) $order->delivery_fee, 2) }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-slate-600">
                    <span>Impuestos</span>

                    <span>
                        ${{ number_format((float) $order->tax, 2) }}
                    </span>
                </div>

                <div class="border-t border-slate-200 pt-3">
                    <div class="flex items-center justify-between text-lg font-extrabold text-slate-900">
                        <span>Total</span>

                        <span>
                            ${{ number_format((float) $order->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Información de la orden --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-extrabold text-slate-900">
                Información del pedido
            </h2>

            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">
                        Número
                    </dt>

                    <dd class="text-right font-bold text-slate-900">
                        {{ $order->order_number }}
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">
                        Fecha
                    </dt>

                    <dd class="text-right font-semibold text-slate-900">
                        {{ $order->created_at->format('M d, Y · g:i A') }}
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">
                        Método de pago
                    </dt>

                    <dd class="text-right font-semibold text-slate-900">
                        @switch($order->payment_method)
                            @case('cash')
                            @case('cash_on_delivery')
                                Efectivo
                                @break

                            @case('stripe')
                            @case('card')
                                Tarjeta
                                @break

                            @default
                                {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                        @endswitch
                    </dd>
                </div>
            </dl>
        </section>

        {{-- Entregado --}}
        @if ($order->status === 'delivered')
            <section class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-emerald-600 shadow-sm">
                    <svg
                        class="h-8 w-8"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="m5 13 4 4L19 7"
                        />
                    </svg>
                </div>

                <h2 class="mt-4 text-xl font-extrabold text-emerald-900">
                    Pedido entregado
                </h2>

                <p class="mt-2 text-sm leading-6 text-emerald-700">
                    Gracias por comprar con nosotros.
                </p>

                @if ($order->delivered_at)
                    <p class="mt-3 text-xs font-semibold text-emerald-600">
                        Entregado {{ $order->delivered_at->format('M d, Y · g:i A') }}
                    </p>
                @endif
            </section>
        @endif

        {{-- Cancelado --}}
        @if ($order->status === 'cancelled')
            <section class="rounded-3xl border border-red-200 bg-red-50 p-6 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-red-600 shadow-sm">
                    <svg
                        class="h-8 w-8"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M6 18 18 6M6 6l12 12"
                        />
                    </svg>
                </div>

                <h2 class="mt-4 text-xl font-extrabold text-red-900">
                    Pedido cancelado
                </h2>

                <p class="mt-2 text-sm leading-6 text-red-700">
                    Este pedido ya no será procesado.
                </p>
            </section>
        @endif

        <section>
               <a
                href="{{ route('customer.orders') }}"
                wire:navigate
                class="flex h-13 flex-1 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700"
            >
                Mis pedidos
            </a>

            <button
                type="button"
                wire:click="refreshOrder"
                wire:loading.attr="disabled"
                wire:target="refreshOrder"
                class="flex h-13 flex-1 items-center justify-center gap-2 rounded-2xl bg-orange-500 px-4 text-sm font-bold text-white shadow-sm disabled:opacity-60"
            >
                <svg
                    wire:loading.class="animate-spin"
                    wire:target="refreshOrder"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4m-4 4a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"
                    />
                </svg>

                Actualizar
            </button>
        </section>
    </main>

    {{-- Barra inferior --}}
    {{-- <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white/95 p-4 backdrop-blur">
        <div class="mx-auto flex max-w-md gap-3">
         
        </div>
    </div> --}}

    @push('scripts')
        <script>
            (() => {
                let deliveryMap = null;
                let driverMarker = null;
                let deliveryMarker = null;
                let routeLine = null;

                function getCoordinates(element) {
                    if (!element) {
                        return null;
                    }

                    const driverLatitude = Number(
                        element.dataset.driverLatitude
                    );

                    const driverLongitude = Number(
                        element.dataset.driverLongitude
                    );

                    const deliveryLatitude = Number(
                        element.dataset.deliveryLatitude
                    );

                    const deliveryLongitude = Number(
                        element.dataset.deliveryLongitude
                    );

                    if (
                        Number.isNaN(driverLatitude) ||
                        Number.isNaN(driverLongitude)
                    ) {
                        return null;
                    }

                    return {
                        driver: {
                            lat: driverLatitude,
                            lng: driverLongitude,
                        },

                        delivery: (
                            !Number.isNaN(deliveryLatitude) &&
                            !Number.isNaN(deliveryLongitude)
                        ) ? {
                            lat: deliveryLatitude,
                            lng: deliveryLongitude,
                        } : null,
                    };
                }

                function initializeDeliveryMap() {
                    const element = document.getElementById(
                        'delivery-map'
                    );

                    const coordinates = getCoordinates(element);

                    if (!element || !coordinates) {
                        return;
                    }

                    /*
                     * Este ejemplo espera que Google Maps JavaScript API
                     * ya esté cargado en el layout.
                     */
                    if (
                        typeof window.google === 'undefined' ||
                        typeof window.google.maps === 'undefined'
                    ) {
                        console.warn(
                            'Google Maps todavía no está disponible.'
                        );

                        return;
                    }

                    if (!deliveryMap) {
                        deliveryMap = new google.maps.Map(element, {
                            center: coordinates.driver,
                            zoom: 14,
                            disableDefaultUI: true,
                            zoomControl: true,
                            mapTypeControl: false,
                            streetViewControl: false,
                            fullscreenControl: false,
                        });

                        driverMarker = new google.maps.Marker({
                            position: coordinates.driver,
                            map: deliveryMap,
                            title: 'Conductor',
                        });

                        if (coordinates.delivery) {
                            deliveryMarker = new google.maps.Marker({
                                position: coordinates.delivery,
                                map: deliveryMap,
                                title: 'Dirección de entrega',
                            });

                            routeLine = new google.maps.Polyline({
                                path: [
                                    coordinates.driver,
                                    coordinates.delivery,
                                ],

                                geodesic: true,
                                strokeOpacity: 0.8,
                                strokeWeight: 4,
                                map: deliveryMap,
                            });

                            const bounds = new google.maps.LatLngBounds();

                            bounds.extend(coordinates.driver);
                            bounds.extend(coordinates.delivery);

                            deliveryMap.fitBounds(bounds);

                            google.maps.event.addListenerOnce(
                                deliveryMap,
                                'bounds_changed',
                                () => {
                                    if (deliveryMap.getZoom() > 16) {
                                        deliveryMap.setZoom(16);
                                    }
                                }
                            );
                        }

                        return;
                    }

                    driverMarker?.setPosition(coordinates.driver);

                    if (coordinates.delivery) {
                        deliveryMarker?.setPosition(
                            coordinates.delivery
                        );

                        routeLine?.setPath([
                            coordinates.driver,
                            coordinates.delivery,
                        ]);
                    }

                    deliveryMap.panTo(coordinates.driver);
                }

                document.addEventListener(
                    'DOMContentLoaded',
                    initializeDeliveryMap
                );

                document.addEventListener(
                    'livewire:navigated',
                    initializeDeliveryMap
                );

                document.addEventListener(
                    'livewire:init',
                    () => {
                        Livewire.hook(
                            'morph.updated',
                            () => {
                                window.setTimeout(
                                    initializeDeliveryMap,
                                    100
                                );
                            }
                        );
                    }
                );
            })();
        </script>
    @endpush
</div>