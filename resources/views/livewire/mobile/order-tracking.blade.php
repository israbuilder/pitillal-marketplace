<div class="space-y-4 pb-24" wire:poll.5s="refreshTracking">
    <section class="rounded-3xl bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase text-indigo-600">Pedido #{{ $order->id }}</p>
        <h1 class="mt-1 text-xl font-black">{{ $order->business?->name }}</h1>
        <p class="mt-2 text-sm text-slate-500">Estado: {{ str_replace('_', ' ', $order->status) }}</p>
    </section>

    <div id="customer-tracking-map" wire:ignore class="h-80 overflow-hidden rounded-3xl shadow-sm"></div>

    <section class="rounded-3xl bg-slate-950 p-5 text-white">
        @if($driverLocation)
            <p class="font-black">Tu repartidor está en camino</p>
            <p class="mt-1 text-sm text-slate-300">Actualizado {{ \Carbon\Carbon::parse($driverLocation['recordedAt'])->diffForHumans() }}</p>
        @else
            <p class="font-black">Esperando ubicación del repartidor</p>
            <p class="mt-1 text-sm text-slate-300">El mapa se actualizará automáticamente cuando acepte y active el GPS.</p>
        @endif
    </section>

    @script
    <script>
        let trackingMap = L.map('customer-tracking-map').setView([
            {{ $order->delivery_latitude ?? 29.7604 }},
            {{ $order->delivery_longitude ?? -95.3698 }}
        ], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(trackingMap);

        let driverMarker = null;

        @if($order->delivery_latitude && $order->delivery_longitude)
            L.marker([{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}])
                .addTo(trackingMap).bindPopup('Entrega');
        @endif

        $wire.on('driver-location-updated', event => {
            const location = event.location;
            if (!location) return;
            const point = [location.lat, location.lng];

            if (!driverMarker) driverMarker = L.marker(point).addTo(trackingMap).bindPopup('Repartidor');
            else driverMarker.setLatLng(point);

            trackingMap.panTo(point);
        });
    </script>
    @endscript
</div>
