import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.data('driverTracker', (config) => ({
        map: null,
        marker: null,
        watcher: null,
        tracking: false,
        message: 'Solicitando permiso de ubicación…',

        pickupLatitude: config.pickupLatitude,
        pickupLongitude: config.pickupLongitude,

        start() {
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

            if (!navigator.geolocation) {
                this.message = 'Este dispositivo no ofrece GPS.';
                return;
            }

            this.message = 'Obteniendo ubicación…';

            this.watcher = navigator.geolocation.watchPosition(
                async (position) => {
                    const {
                        latitude,
                        longitude,
                        accuracy,
                        heading,
                        speed
                    } = position.coords;

                    const point = [latitude, longitude];

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
                        console.error(error);

                        this.message =
                            'Ubicación obtenida, pero no pudo guardarse.';
                    }
                },

                (error) => {
                    this.tracking = false;

                    switch (error.code) {
                        case 1:
                            this.message =
                                'Permiso de ubicación denegado.';
                            break;

                        case 2:
                            this.message =
                                'Ubicación no disponible.';
                            break;

                        case 3:
                            this.message =
                                'La ubicación tardó demasiado.';
                            break;

                        default:
                            this.message =
                                'No se pudo obtener la ubicación.';
                    }
                },

                {
                    enableHighAccuracy: true,
                    maximumAge: 5000,
                    timeout: 15000
                }
            );
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
});

