<div class="space-y-5 pb-24">
    <form wire:submit="save" class="space-y-3 rounded-3xl bg-white p-5 shadow-sm">
        <h2 class="text-lg font-black">{{ $editingId ? 'Editar producto' : 'Nuevo producto' }}</h2>
        <input wire:model="name" placeholder="Nombre" class="w-full rounded-2xl border-slate-200 bg-slate-50">
        
        <textarea wire:model="description" placeholder="Descripción" class="w-full rounded-2xl border-slate-200 bg-slate-50"></textarea>
        <div class="grid grid-cols-2 gap-3">
            <input wire:model="price" type="number" step=".01" placeholder="Precio" class="rounded-2xl border-slate-200 bg-slate-50">
            <input wire:model="stock" type="number" placeholder="Stock" class="rounded-2xl border-slate-200 bg-slate-50">
        </div>
       <div class="space-y-3">

    <label class="block text-sm font-bold text-slate-700">
        Imagen del producto
    </label>

    <input
        type="file"
        wire:model="image"
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
    @if ($image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
    <img
        src="{{ $image->temporaryUrl() }}"
        class="h-36 w-full rounded-2xl object-cover"
    >
@elseif($image)
    <img
        src="{{ Storage::url($image) }}"
        class="h-36 w-full rounded-2xl object-cover"
    >
@endif

</div>
        <label class="flex items-center gap-2 text-sm font-bold">
            <input type="checkbox" wire:model="active" class="rounded border-slate-300"> Producto activo
        </label>
        <div class="flex gap-2">
            <button class="flex-1 rounded-2xl bg-indigo-600 py-3 font-black text-white">Guardar</button>
            @if($editingId)<button type="button" wire:click="resetForm" class="rounded-2xl bg-slate-100 px-4 font-bold">Cancelar</button>@endif
        </div>
    </form>

    <div class="space-y-3">
        @foreach($products as $product)
            <article class="flex gap-3 rounded-3xl bg-white p-4 shadow-sm">
              <img
    src="{{ $product->image
        ? Storage::url($product->image)
        : asset('assets/images/product-placeholder.jpg') }}"
    class="h-20 w-20 rounded-2xl object-cover"
>
                <div class="min-w-0 flex-1">
                    <h3 class="truncate font-black">{{ $product->name }}</h3>
                    <p class="text-sm text-slate-500">${{ number_format($product->price, 2) }} · Stock {{ $product->stock }}</p>
                    <div class="mt-3 flex gap-2">
                        <button wire:click="edit({{ $product->id }})" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold">Editar</button>
                        <button wire:click="delete({{ $product->id }})" wire:confirm="¿Eliminar producto?" class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600">Eliminar</button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
