<form wire:submit="placeOrder" class="space-y-4 pb-24" x-data="{
    locating: false,
    locate() {
        if (!navigator.geolocation) return;
        this.locating = true;
        navigator.geolocation.getCurrentPosition(position => {
            $wire.set('delivery_latitude', position.coords.latitude);
            $wire.set('delivery_longitude', position.coords.longitude);
            this.locating = false;
        }, () => this.locating = false, { enableHighAccuracy: true });
    }
}">
    <section class="rounded-3xl bg-white p-4 shadow-sm">
        <p class="text-xs font-bold uppercase text-indigo-600">{{ $business->name }}</p>
        <p class="mt-1 text-sm text-slate-500">Entrega estimada: {{ $business->estimated_minutes }} minutos</p>
    </section>

    @foreach(['full_name' => 'Nombre completo', 'phone' => 'Teléfono', 'address' => 'Dirección de entrega'] as $field => $label)
        <label class="block">
            <span class="mb-1 block text-sm font-semibold">{{ $label }}</span>
            <input wire:model="{{ $field }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            @error($field)<span class="mt-1 text-xs text-rose-600">{{ $message }}</span>@enderror
        </label>
    @endforeach

    <button type="button" @click="locate" class="w-full rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700">
        <span x-show="!locating">Usar mi ubicación actual para el mapa</span>
        <span x-show="locating">Obteniendo ubicación…</span>
    </button>

    <label class="block">
        <span class="mb-1 block text-sm font-semibold">Notas</span>
        <textarea wire:model="notes" rows="3" class="w-full rounded-2xl border border-slate-300 px-4 py-3"></textarea>
    </label>

    <div>
        <span class="mb-2 block text-sm font-semibold">Método de pago</span>
        <div class="grid grid-cols-2 gap-3">
            <label class="rounded-2xl border p-3 {{ $payment_method === 'cash' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200' }}"><input type="radio" wire:model.live="payment_method" value="cash" class="mr-2">Efectivo</label>
            <label class="rounded-2xl border p-3 {{ $payment_method === 'card' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200' }}"><input type="radio" wire:model.live="payment_method" value="card" class="mr-2">Tarjeta</label>
        </div>
    </div>

    <div class="rounded-2xl bg-slate-100 p-4">
        <div class="flex justify-between"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
        <div class="mt-2 flex justify-between"><span>Envío</span><span>${{ number_format($business->delivery_fee, 2) }}</span></div>
        <div class="mt-3 flex justify-between border-t pt-3 text-lg font-bold"><span>Total</span><span>${{ number_format($subtotal + (float) $business->delivery_fee, 2) }}</span></div>
    </div>

    <button type="submit" class="w-full rounded-2xl bg-indigo-600 px-4 py-4 font-bold text-white">
        {{ $payment_method === 'card' ? 'Continuar al pago seguro' : 'Confirmar pedido' }}
    </button>
</form>
