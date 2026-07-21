<form wire:submit="save" class="space-y-4 pb-24">
    @if(session('success'))
        <div class="rounded-2xl bg-emerald-100 p-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-3xl bg-white p-5 shadow-sm space-y-4">
        @foreach([
            ['name','Nombre del negocio','text'],
            ['phone','Teléfono','tel'],
            ['email','Correo','email'],
            ['address','Dirección','text'],
            ['delivery_fee','Costo de entrega','number'],
            ['estimated_minutes','Tiempo estimado (minutos)','number'],
        ] as [$model,$label,$type])
            <label class="block">
                <span class="mb-1 block text-sm font-bold text-slate-700">{{ $label }}</span>
                <input type="{{ $type }}" wire:model="{{ $model }}" class="w-full rounded-2xl border-slate-200 bg-slate-50">
                @error($model)<span class="text-xs text-rose-600">{{ $message }}</span>@enderror
            </label>
        @endforeach

        <label class="block">
            <span class="mb-1 block text-sm font-bold text-slate-700">Descripción</span>
            <textarea wire:model="description" rows="4" class="w-full rounded-2xl border-slate-200 bg-slate-50"></textarea>
        </label>

        <div class="space-y-3">

    <label class="block text-sm font-bold text-slate-700">
        Logo del negocio
    </label>

    <input
        type="file"
        wire:model="logo_path"
        accept="image/*"
        class="block w-full rounded-2xl border border-slate-200 bg-slate-50 p-3"
    >

    <div wire:loading wire:target="image">
        <p class="text-sm text-indigo-600">
            Subiendo imagen...
        </p>
    </div>
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($logo_path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile))
        <img
            src="{{ $logo_path->temporaryUrl() }}"
            class="h-36 w-full rounded-2xl object-cover"
        >
    @elseif($logo_path)
        <img
            src="{{ Storage::url($logo_path) }}"
            class="h-36 w-full rounded-2xl object-cover"
        >
    @endif

</div>
        <div class="grid grid-cols-3 gap-3">
           <button
    type="button"
    onclick="getCurrentLocation()"
    class="w-full rounded-2xl bg-slate-100 py-3 font-bold"
>
    📍 Usar mi ubicación actual
</button>
<input wire:model="lat" placeholder="Latitud" class="rounded-2xl border-slate-200 bg-slate-50">
            <input wire:model="lng" placeholder="Longitud" class="rounded-2xl border-slate-200 bg-slate-50">
        </div>
    </div>

    <button class="w-full rounded-2xl bg-indigo-600 py-4 font-black text-white shadow-lg">Guardar negocio</button>
</form>

<script>
function getCurrentLocation() {

    if (!navigator.geolocation) {
        alert("Tu navegador no soporta geolocalización.");
        return;
    }

    navigator.geolocation.getCurrentPosition(

        function(position){

            @this.set('lat', position.coords.latitude);
            @this.set('lng', position.coords.longitude);

        },

        function(error){
            alert("No fue posible obtener tu ubicación.");
        }

    );

}
</script>
