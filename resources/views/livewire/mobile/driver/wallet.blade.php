<div class="min-h-screen bg-slate-50 pb-28">
    <div class="mx-auto max-w-xl px-4 py-6">
        <header class="mb-6">
            <p class="text-sm font-semibold text-slate-500">
                Monedero del Conductor
            </p>

            <h1 class="text-2xl font-black text-slate-900">
                Balance
            </h1>
        </header>

        @if (request()->boolean('cancelled'))
            <div
                class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800"
            >
                El pago fué cancelado. Su mondero no se recargo.
            </div>
        @endif

        <section
            class="mb-6 overflow-hidden rounded-3xl bg-slate-950 p-6 text-white shadow-xl"
        >
            <p class="text-sm font-semibold text-slate-300">
                Balance Disponible
            </p>

            <div class="mt-2 text-4xl font-black tracking-tight">
               
                {{ $wallet->formatted_balance }}
            </div>

            <p class="mt-3 text-sm text-slate-400">
                Este balance es usado para aceptar viajes.
            </p>
        </section>

        <section class="mb-6 rounded-3xl bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-900">
                Agregar Balance
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Selecciona la cantidad ó entra la cantidad deseada.
            </p>

            <div class="mt-4 grid grid-cols-4 gap-2">
                @foreach ($presetAmounts as $preset)
                    <button
                        type="button"
                        wire:click="selectAmount({{ $preset }})"
                        class="rounded-2xl border px-3 py-3 text-sm font-black transition
                            {{ (float) $amount === (float) $preset
                                ? 'border-slate-950 bg-slate-950 text-white'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                    >
                        ${{ number_format($preset, 0) }}
                    </button>
                @endforeach
            </div>

            <form
                wire:submit="purchaseBalance"
                class="mt-5"
            >
                <label
                    for="amount"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Cantidad
                </label>

                <div class="relative">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-black text-slate-500"
                    >
                        $
                    </span>

                    <input
                        id="amount"
                        type="number"
                        min="10"
                        max="500"
                        step="0.01"
                        wire:model="amount"
                        class="w-full rounded-2xl border border-slate-200 py-4 pl-9 pr-4 text-lg font-black outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                </div>

                @error('amount')
                    <p class="mt-2 text-sm font-bold text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="purchaseBalance"
                    class="mt-4 flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-4 text-sm font-black text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span
                        wire:loading.remove
                        wire:target="purchaseBalance"
                    >
                        Continuar Pago Seguro
                    </span>

                    <span
                        wire:loading
                        wire:target="purchaseBalance"
                    >
                        Abriendo Pasarela de Pago...
                    </span>
                </button>
            </form>
        </section>

        <section class="rounded-3xl bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900">
                    Atividad reciente
                </h2>

                <span class="text-xs font-bold text-slate-400">
                    Últimos 25
                </span>
            </div>

            <div class="space-y-3">
                @forelse ($transactions as $transaction)
                    <article
                        wire:key="wallet-transaction-{{ $transaction->id }}"
                        class="flex items-center justify-between rounded-2xl border border-slate-100 p-4"
                    >
                        <div class="min-w-0 pr-4">
                            <p class="truncate text-sm font-black text-slate-800">
                                {{ match ($transaction->reason) {
                                    'stripe_top_up' => 'Balance purchase',
                                    'order_acceptance' => 'Order acceptance fee',
                                    'order_acceptance_refund' => 'Order fee refund',
                                    'admin_adjustment' => 'Balance adjustment',
                                    default => $transaction->description ?? 'Wallet transaction',
                                } }}
                            </p>

                            <p class="mt-1 text-xs font-semibold text-slate-400">
                                {{ $transaction->created_at->format('M j, Y g:i A') }}

                                @if ($transaction->order_id)
                                    · Orden #{{ $transaction->order_id }}
                                @endif
                            </p>
                        </div>

                        <div class="text-right">
                            <p
                                class="text-sm font-black
                                    {{ in_array($transaction->type, ['credit', 'refund'], true)
                                        ? 'text-emerald-600'
                                        : 'text-red-600' }}"
                            >
                                {{ in_array($transaction->type, ['credit', 'refund'], true)
                                    ? '+'
                                    : '-' }}
                                {{ $transaction->formatted_amount }}
                            </p>

                            <p class="mt-1 text-xs font-bold text-slate-400">
                                Balance:
                                ${{ number_format(
                                    $transaction->balance_after_cents / 100,
                                    2
                                ) }}
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl bg-slate-50 p-6 text-center">
                        <p class="text-sm font-bold text-slate-500">
                           Sin actividad de Monedero aún.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>