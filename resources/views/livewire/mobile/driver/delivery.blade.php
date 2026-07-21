<div class="space-y-4 pb-24" x-data="driverTracker()" x-init="start()">
    <div wire:offline class="rounded-2xl bg-amber-100 p-3 text-sm font-bold text-amber-900">
        Sin conexión. El GPS continuará intentando enviar la ubicación.
    </div>

    <section class="rounded-3xl bg-slate-950 p-5 text-white">
        <p class="text-xs uppercase tracking-[.2em] text-slate-400">Entrega #{{ $order->id }}</p>
        <h1 class="mt-2 text-xl font-black">{{ $order->business?->name }}</h1>
        <p class="mt-3 text-sm text-slate-300">{{ $order->delivery_address }}</p>
        <div class="mt-4 flex items-center gap-2 text-xs font-bold">
            <span class="h-2.5 w-2.5 rounded-full" :class="tracking ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500'"></span>
            <span x-text="message"></span>
        </div>
    </section>

    <div id="driver-map" wire:ignore class="h-72 overflow-hidden rounded-3xl shadow-sm"></div>

    <section class="grid gap-3">
        @if($order->status === 'accepted' || $order->status === 'ready')
            <button wire:click="markPickedUp" class="rounded-2xl bg-amber-400 py-4 font-black text-amber-950">Confirmar recolección</button>
        @elseif($order->status === 'picked_up')
            <button wire:click="startDelivery" class="rounded-2xl bg-indigo-600 py-4 font-black text-white">Iniciar entrega</button>
        @elseif($order->status === 'on_the_way')
            <button wire:click="markDelivered" wire:confirm="¿Confirmas que entregaste el pedido?" class="rounded-2xl bg-emerald-500 py-4 font-black text-emerald-950">Marcar entregado</button>
        @endif
    </section>

    @script
    <script>
        Alpine.data('driverTracker', () => ({
            map: null,
            marker: null,
            watcher: null,
            tracking: false,
            message: 'Solicitando permiso de ubicación…',

            start() {
                this.map = L.map('driver-map').setView([
                    {{ $order->pickup_latitude ?? 29.7604 }},
                    {{ $order->pickup_longitude ?? -95.3698 }}
                ], 14);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);

                if (!navigator.geolocation) {
                    this.message = 'Este dispositivo no ofrece GPS.';
                    return;
                }

                this.watcher = navigator.geolocation.watchPosition(
                    async position => {
                        const { latitude, longitude, accuracy, heading, speed } = position.coords;
                        const point = [latitude, longitude];

                        if (!this.marker) this.marker = L.marker(point).addTo(this.map);
                        else this.marker.setLatLng(point);

                        this.map.panTo(point);
                        this.tracking = true;
                        this.message = `Compartiendo ubicación · precisión ${Math.round(accuracy)} m`;

                        await $wire.updateLocation(latitude, longitude, accuracy, heading, speed);
                    },
                    error => {
                        this.tracking = false;
                        this.message = error.code === 1 ? 'Permiso de ubicación denegado.' : 'No se pudo obtener la ubicación.';
                    },
                    { enableHighAccuracy: true, maximumAge: 5000, timeout: 15000 }
                );
            }
        }));
    </script>
    @endscript
</div>
