<div class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto max-w-md">
        <section class="rounded-3xl bg-white p-7 text-center shadow-sm">
            <div
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl"
            >
                ✓
            </div>

            <h1 class="mt-5 text-2xl font-black text-slate-900">
                Payment received
            </h1>

            @if ($topUp?->status === 'paid')
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    {{ $topUp->formatted_amount }} has been added to your
                    driver wallet.
                </p>
            @else
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Stripe received your payment. We are confirming it and
                    your balance will update automatically.
                </p>
            @endif

            <a
                wire:navigate
                href="{{ route('driver.wallet.index') }}"
                class="mt-6 block rounded-2xl bg-slate-950 px-5 py-4 text-sm font-black text-white"
            >
                View wallet
            </a>

            <a
                wire:navigate
                href="{{ route('driver.orders') }}"
                class="mt-3 block rounded-2xl bg-slate-100 px-5 py-4 text-sm font-black text-slate-700"
            >
                View available orders
            </a>
        </section>
    </div>
</div>