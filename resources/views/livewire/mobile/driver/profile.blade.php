<div class="min-h-screen bg-slate-50 pb-28">
   
    <main class="mx-auto max-w-3xl space-y-5 px-4 py-5">
        @if (session('success'))
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700"
                x-data="{ visible: true }"
                x-show="visible"
                x-init="setTimeout(() => visible = false, 4000)"
            >
                {{ session('success') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-3xl bg-white shadow-sm">
            <div class="bg-linear-to-br from-indigo-600 to-violet-600 px-5 py-8 text-white">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        @if (
                            $profile_photo instanceof
                            \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                        )
                            <img
                                src="{{ $profile_photo->temporaryUrl() }}"
                                alt="Vista previa"
                                style="width:50px"
                                class="h-24 w-24 rounded-full border-4 border-white/30 object-cover shadow-lg"
                            >
                        @elseif ($existingProfilePhoto)
                            <img
                                style="width:50px"
                                src="{{ Storage::disk('public')->url($existingProfilePhoto) }}"
                                alt="{{ $name }}"
                                class="h-24 w-24 rounded-full border-4 border-white/30 object-cover shadow-lg"
                            >
                        @else
                            <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-white/30 bg-white/20 text-3xl font-black shadow-lg">
                                {{ mb_strtoupper(mb_substr($name ?: 'D', 0, 1)) }}
                            </div>
                        @endif

                        <span
                            class="absolute bottom-1 right-1 h-5 w-5 rounded-full border-4 border-indigo-600
                                {{ $is_available
                                    ? 'bg-emerald-400'
                                    : 'bg-slate-400' }}"
                        ></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-2xl text-black" style="color:#000 !important">
                            {{ $name ?: 'Conductor' }}
                        </h2>

                        <p class="mt-1 text-sm text-indigo-100">
                            {{ $vehicle_model ?: 'Vehículo no registrado' }}
                        </p>

                        <button
                            type="button"
                            wire:click="toggleAvailability"
                            wire:loading.attr="disabled"
                            wire:target="toggleAvailability"
                            style="color:#fff"
                            class="mt-4 rounded-full px-4 py-2 text-xs font-black shadow-sm transition disabled:opacity-50
                                {{ $is_available
                                    ? 'bg-emerald-400 text-emerald-950'
                                    : 'bg-black text-black' }}"
                        >
                            <span wire:loading.remove wire:target="toggleAvailability">
                                {{ $is_available
                                    ? 'Disponible'
                                    : 'No disponible' }}
                            </span>

                            <span wire:loading wire:target="toggleAvailability">
                                Actualizando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 divide-x divide-slate-100">
                <div class="p-5 text-center">
                    <p class="text-2xl font-black text-slate-900">
                        {{ $this->activeOrdersCount }}
                    </p>

                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Activas
                    </p>
                </div>

                <div class="p-5 text-center">
                    <p class="text-2xl font-black text-slate-900">
                        {{ $this->deliveredOrdersCount }}
                    </p>

                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Entregadas
                    </p>
                </div>
            </div>
        </section>

        <form
            wire:submit="save"
            class="space-y-5"
        >
            <section class="space-y-4 rounded-3xl bg-white p-5 shadow-sm">
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        Foto de perfil
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Usa una foto clara para que el cliente pueda reconocerte.
                    </p>
                </div>

                <label
                    for="profile-photo"
                    class="flex cursor-pointer items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-5 py-6 text-center transition hover:border-indigo-300"
                >
                    <div>
                        <div class="text-3xl">📷</div>

                        <p class="mt-2 text-sm font-black text-slate-700">
                            Seleccionar fotografía
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            JPG, PNG o WEBP. Máximo 5 MB.
                        </p>
                    </div>
                </label>

                <input
                    id="profile-photo"
                    type="file"
                    wire:model="profile_photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                >

                <div
                    wire:loading
                    wire:target="profile_photo"
                    class="rounded-2xl bg-indigo-50 p-3 text-center text-sm font-bold text-indigo-600"
                >
                    Procesando fotografía...
                </div>

                @error('profile_photo')
                    <p class="text-sm font-bold text-rose-600">
                        {{ $message }}
                    </p>
                @enderror

                @if ($existingProfilePhoto)
                    <button
                        type="button"
                        wire:click="removePhoto"
                        wire:confirm="¿Quieres eliminar tu foto de perfil?"
                        class="w-full rounded-2xl bg-rose-50 py-3 text-sm font-black text-rose-600"
                    >
                        Eliminar foto actual
                    </button>
                @endif
            </section>

            <section class="space-y-4 rounded-3xl bg-white p-5 shadow-sm">
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        Información personal
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Esta información se utiliza para contactarte.
                    </p>
                </div>

                <div>
                    <label
                        for="driver-name"
                        class="mb-1.5 block text-sm font-bold text-slate-700"
                    >
                        Nombre completo
                    </label>

                    <input
                        id="driver-name"
                        wire:model="name"
                        type="text"
                        placeholder="Nombre completo"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50"
                    >

                    @error('name')
                        <p class="mt-1 text-sm font-bold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="driver-email"
                        class="mb-1.5 block text-sm font-bold text-slate-700"
                    >
                        Correo electrónico
                    </label>

                    <input
                        id="driver-email"
                        wire:model="email"
                        type="email"
                        placeholder="correo@ejemplo.com"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50"
                    >

                    @error('email')
                        <p class="mt-1 text-sm font-bold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="driver-phone"
                        class="mb-1.5 block text-sm font-bold text-slate-700"
                    >
                        Teléfono
                    </label>

                    <input
                        id="driver-phone"
                        wire:model="phone"
                        type="tel"
                        placeholder="(713) 555-1234"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50"
                    >

                    @error('phone')
                        <p class="mt-1 text-sm font-bold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </section>

            <section class="space-y-4 rounded-3xl bg-white p-5 shadow-sm">
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        Vehículo
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Registra el vehículo que utilizarás para las entregas.
                    </p>
                </div>

                <div>
                    <label
                        for="vehicle-type"
                        class="mb-1.5 block text-sm font-bold text-slate-700"
                    >
                        Tipo de vehículo
                    </label>

                    <select
                        id="vehicle-type"
                        wire:model="vehicle_type"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50"
                    >
                        <option value="">Seleccionar</option>
                        <option value="motorcycle">Motocicleta</option>
                        <option value="car">Automóvil</option>
                        <option value="truck">Camioneta</option>
                        <option value="bicycle">Bicicleta</option>
                        <option value="other">Otro</option>
                    </select>

                    @error('vehicle_type')
                        <p class="mt-1 text-sm font-bold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="vehicle-model"
                        class="mb-1.5 block text-sm font-bold text-slate-700"
                    >
                        Marca y modelo
                    </label>

                    <input
                        id="vehicle-model"
                        wire:model="vehicle_model"
                        type="text"
                        placeholder="Ej. Honda CB500X 2023"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50"
                    >

                    @error('vehicle_model')
                        <p class="mt-1 text-sm font-bold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label
                            for="vehicle-color"
                            class="mb-1.5 block text-sm font-bold text-slate-700"
                        >
                            Color
                        </label>

                        <input
                            id="vehicle-color"
                            wire:model="vehicle_color"
                            type="text"
                            placeholder="Negro"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50"
                        >

                        @error('vehicle_color')
                            <p class="mt-1 text-sm font-bold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="plate-number"
                            class="mb-1.5 block text-sm font-bold text-slate-700"
                        >
                            Placa
                        </label>

                        <input
                            id="plate-number"
                            wire:model="plate_number"
                            type="text"
                            placeholder="ABC-1234"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 uppercase"
                        >

                        @error('plate_number')
                            <p class="mt-1 text-sm font-bold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label
                        for="license-number"
                        class="mb-1.5 block text-sm font-bold text-slate-700"
                    >
                        Número de licencia
                    </label>

                    <input
                        id="license-number"
                        wire:model="license_number"
                        type="text"
                        placeholder="Número de licencia"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50"
                    >

                    @error('license_number')
                        <p class="mt-1 text-sm font-bold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </section>

            <section
                x-data="{
                    locating: false,

                    getCurrentLocation() {
                        if (!navigator.geolocation) {
                            alert('Tu navegador no soporta geolocalización.');
                            return;
                        }

                        this.locating = true;

                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                $wire.set(
                                    'lat',
                                    position.coords.latitude
                                );

                                $wire.set(
                                    'lng',
                                    position.coords.longitude
                                );

                                this.locating = false;
                            },
                            (error) => {
                                this.locating = false;

                                let message =
                                    'No fue posible obtener tu ubicación.';

                                if (error.code === 1) {
                                    message =
                                        'Debes permitir el acceso a tu ubicación.';
                                } else if (error.code === 2) {
                                    message =
                                        'Tu ubicación no está disponible.';
                                } else if (error.code === 3) {
                                    message =
                                        'La solicitud tardó demasiado.';
                                }

                                alert(message);
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 15000,
                                maximumAge: 0
                            }
                        );
                    }
                }"
                class="space-y-4 rounded-3xl bg-white p-5 shadow-sm"
            >
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        Ubicación
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        La ubicación permite mostrarte órdenes cercanas.
                    </p>
                </div>

                <button
                    type="button"
                    x-on:click="getCurrentLocation"
                    x-bind:disabled="locating"
                    class="w-full rounded-2xl bg-indigo-50 py-4 text-sm font-black text-indigo-600 disabled:opacity-50"
                >
                    <span x-show="!locating">
                        📍 Usar mi ubicación actual
                    </span>

                    <span x-show="locating" x-cloak>
                        Obteniendo ubicación...
                    </span>
                </button>

                @if ($lat && $lng)
                    <div class="rounded-2xl bg-emerald-50 p-4">
                        <p class="text-sm font-black text-emerald-700">
                            Ubicación registrada
                        </p>

                        <p class="mt-1 text-xs text-emerald-600">
                            {{ number_format((float) $lat, 6) }},
                            {{ number_format((float) $lng, 6) }}
                        </p>
                    </div>
                @endif

                @error('lat')
                    <p class="text-sm font-bold text-rose-600">
                        {{ $message }}
                    </p>
                @enderror

                @error('lng')
                    <p class="text-sm font-bold text-rose-600">
                        {{ $message }}
                    </p>
                @enderror
            </section>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save,profile_photo"
                class="w-full rounded-2xl bg-indigo-600 py-4 font-black text-white shadow-lg disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">
                    Guardar perfil
                </span>

                <span wire:loading wire:target="save">
                    Guardando...
                </span>
            </button>
        </form>
    </main>
</div>