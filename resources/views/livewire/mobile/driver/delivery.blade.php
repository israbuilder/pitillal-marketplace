<div class="space-y-4 pb-24">
    <div
        x-data="driverTracker({
            pickupLatitude: @js($order->pickup_latitude ?? 29.7604),
            pickupLongitude: @js($order->pickup_longitude ?? -95.3698)
        })"
        x-init="start()"
        class="space-y-4"
    >
        <div
            wire:offline
            class="rounded-2xl bg-amber-100 p-3 text-sm font-bold text-amber-900"
        >
            Sin conexión. El GPS continuará intentando enviar la ubicación.
        </div>

        <section class="rounded-3xl bg-slate-950 p-5 text-white">
            <p class="text-xs uppercase tracking-[.2em] text-slate-400">
                Entrega #{{ $order->id }}
            </p>

            <h1 class="mt-2 text-xl font-black">
                {{ $order->business?->name }}
            </h1>

            <p class="mt-3 text-sm text-slate-300">
                {{ $order->delivery_address }}
            </p>

            <div class="mt-4 flex items-center gap-2 text-xs font-bold">
                <span
                    class="h-2.5 w-2.5 rounded-full"
                    :class="tracking
                        ? 'bg-emerald-400 animate-pulse'
                        : 'bg-slate-500'"
                ></span>

                <span x-text="message"></span>
            </div>
        </section>

        <div
            x-ref="map"
            wire:ignore
            class="h-72 overflow-hidden rounded-3xl shadow-sm"
        ></div>

        <section class="grid gap-3">
            @if ($order->status === 'accepted' || $order->status === 'ready')
                <button
                    type="button"
                    wire:click="markPickedUp"
                    class="rounded-2xl bg-amber-400 py-4 font-black text-amber-950"
                >
                    Confirmar recolección
                </button>
            @elseif ($order->status === 'picked_up')
                <button
                    type="button"
                    wire:click="startDelivery"
                    class="rounded-2xl bg-indigo-600 py-4 font-black text-white"
                >
                    Iniciar entrega
                </button>
            @elseif ($order->status === 'on_the_way')
                <button
                    type="button"
                    wire:click="markDelivered"
                    wire:confirm="¿Confirmas que entregaste el pedido?"
                    class="rounded-2xl bg-emerald-500 py-4 font-black text-emerald-950"
                >
                    Marcar entregado
                </button>
            @endif
        </section>
    </div>

    @script
        <script>
            Alpine.data('driverTracker', (config) => ({
                map: null,
                marker: null,
                watcher: null,

                tracking: false,
                message: 'Solicitando permiso de ubicación…',

                pickupLatitude: config.pickupLatitude,
                pickupLongitude: config.pickupLongitude,

                start() {
                    if (this.map) {
                        return;
                    }

                    this.initializeMap();
                    this.startTracking();
                },

                initializeMap() {
                    this.map = L.map(this.$refs.map).setView(
                        [
                            this.pickupLatitude,
                            this.pickupLongitude
                        ],
                        14
                    );

                    L.tileLayer(
                        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                        {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap'
                        }
                    ).addTo(this.map);

                    setTimeout(() => {
                        this.map.invalidateSize();
                    }, 100);
                },

                startTracking() {
                    if (!navigator.geolocation) {
                        this.tracking = false;
                        this.message = 'Este dispositivo no ofrece GPS.';
                        return;
                    }

                    if (this.watcher !== null) {
                        navigator.geolocation.clearWatch(this.watcher);
                    }

                    this.message = 'Obteniendo ubicación…';

                    this.watcher = navigator.geolocation.watchPosition(
                        async (position) => {
                            await this.handlePosition(position);
                        },

                        (error) => {
                            this.handleLocationError(error);
                        },

                        {
                            enableHighAccuracy: true,
                            maximumAge: 5000,
                            timeout: 15000
                        }
                    );
                },

                async handlePosition(position) {
                    const {
                        latitude,
                        longitude,
                        accuracy,
                        heading,
                        speed
                    } = position.coords;

                    const point = [
                        latitude,
                        longitude
                    ];

                    if (!this.marker) {
                        this.marker = L.marker(point).addTo(this.map);
                    } else {
                        this.marker.setLatLng(point);
                    }

                    this.map.panTo(point);

                    this.tracking = true;
                    this.message =
                        `Compartiendo ubicación · precisión ${Math.round(accuracy)} m`;

                    try {
                        await this.$wire.updateLocation(
                            latitude,
                            longitude,
                            accuracy,
                            heading,
                            speed
                        );
                    } catch (error) {
                        console.error(
                            'Error guardando ubicación:',
                            error
                        );

                        this.message =
                            'Ubicación obtenida, pero no pudo guardarse.';
                    }
                },

                handleLocationError(error) {
                    console.error('Geolocation error:', {
                        code: error.code,
                        message: error.message
                    });

                    this.tracking = false;

                    switch (error.code) {
                        case 1:
                            this.message =
                                'Permiso de ubicación denegado.';
                            break;

                        case 2:
                            this.message =
                                'La ubicación no está disponible.';
                            break;

                        case 3:
                            this.message =
                                'La solicitud de ubicación tardó demasiado.';
                            break;

                        default:
                            this.message =
                                'No se pudo obtener la ubicación.';
                    }
                },

                stop() {
                    if (this.watcher !== null) {
                        navigator.geolocation.clearWatch(this.watcher);
                        this.watcher = null;
                    }

                    this.tracking = false;
                    this.message = 'Seguimiento detenido.';
                },

                destroy() {
                    this.stop();

                    if (this.map) {
                        this.map.remove();
                        this.map = null;
                    }
                }
            }));
        </script>
    @endscript
</div>