<div class="space-y-6">

    {{-- Header --}}
    <div style="background: #E52471" class="rounded-3xl bg-libear-to-r from-indigo-600 to-violet-600 p-6 text-white shadow-lg">

        <div class="flex items-center gap-4">

            <div
                class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-white/30 bg-white/20 text-3xl font-black">
                {{ strtoupper(substr(auth()->user()->name ?? 'I', 0, 1)) }}
            </div>

            <div class="flex-1">

                <h1 class="text-xl font-black">
                    {{ auth()->user()->name }}
                </h1>

                <p class="text-sm text-indigo-100">
                    {{ auth()->user()->email }}
                </p>

                @if(auth()->user()->phone)
                    <p class="mt-1 text-xs text-indigo-200">
                        {{ auth()->user()->phone }}
                    </p>
                @endif

            </div>

        </div>

    </div>

    {{-- Orders Resume --}}
    <div class="grid grid-cols-3 gap-3">

        <div class="rounded-2xl bg-white p-4 text-center shadow-sm">

            <div class="text-2xl font-black text-indigo-600">
                {{ auth()->user()->orders()->count() }}
            </div>

            <div class="mt-1 text-xs text-slate-500">
                Orders
            </div>

        </div>

        <div class="rounded-2xl bg-white p-4 text-center shadow-sm">

            <div class="text-2xl font-black text-emerald-600">
                {{ auth()->user()->orders()->where('status','completed')->count() }}
            </div>

            <div class="mt-1 text-xs text-slate-500">
                Completed
            </div>

        </div>

        <div class="rounded-2xl bg-white p-4 text-center shadow-sm">

            <div class="text-2xl font-black text-orange-500">
                {{ auth()->user()->orders()->where('status','pending')->count() }}
            </div>

            <div class="mt-1 text-xs text-slate-500">
                Pending
            </div>

        </div>

    </div>

    {{-- Account --}}
    <div class="overflow-hidden rounded-3xl bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-3">
            <h2 class="font-black">
                Account
            </h2>
        </div>

        <a
            wire:navigate
            href="{{ route('customer.home') }}"
            class="flex items-center justify-between px-5 py-4 transition hover:bg-slate-50">

            <div class="flex items-center gap-3">

                <div class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-100">
                    📍
                </div>

                <div>
                    <div class="font-semibold">
                        My Addresses
                    </div>

                    <div class="text-xs text-slate-500">
                        Delivery locations
                    </div>
                </div>

            </div>

            <span class="text-slate-400">›</span>

        </a>

        <a
            href="#"
            class="flex items-center justify-between border-t border-slate-100 px-5 py-4 transition hover:bg-slate-50">

            <div class="flex items-center gap-3">

                <div class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-100">
                    💳
                </div>

                <div>
                    <div class="font-semibold">
                        Payment Methods
                    </div>

                    <div class="text-xs text-slate-500">
                        Cards & Wallets
                    </div>
                </div>

            </div>

            <span class="text-slate-400">›</span>

        </a>

        <a
            href="#"
            class="flex items-center justify-between border-t border-slate-100 px-5 py-4 transition hover:bg-slate-50">

            <div class="flex items-center gap-3">

                <div class="grid h-10 w-10 place-items-center rounded-xl bg-yellow-100">
                    🔔
                </div>

                <div>
                    <div class="font-semibold">
                        Notifications
                    </div>

                    <div class="text-xs text-slate-500">
                        Push notifications
                    </div>
                </div>

            </div>

            <span class="text-slate-400">›</span>

        </a>

    </div>

    {{-- Support --}}
    <div class="overflow-hidden rounded-3xl bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-3">
            <h2 class="font-black">
                Support
            </h2>
        </div>

        <a
            href="#"
            class="flex items-center justify-between px-5 py-4 hover:bg-slate-50">

            <div class="flex items-center gap-3">

                <div class="grid h-10 w-10 place-items-center rounded-xl bg-blue-100">
                    ❓
                </div>

                <span class="font-semibold">
                    Help Center
                </span>

            </div>

            <span class="text-slate-400">›</span>

        </a>

        <a
            href="#"
            class="flex items-center justify-between border-t border-slate-100 px-5 py-4 hover:bg-slate-50">

            <div class="flex items-center gap-3">

                <div class="grid h-10 w-10 place-items-center rounded-xl bg-purple-100">
                    💬
                </div>

                <span class="font-semibold">
                    Contact Support
                </span>

            </div>

            <span class="text-slate-400">›</span>

        </a>

        <a
            href="#"
            class="flex items-center justify-between border-t border-slate-100 px-5 py-4 hover:bg-slate-50">

            <div class="flex items-center gap-3">

                <div class="grid h-10 w-10 place-items-center rounded-xl bg-slate-200">
                    📄
                </div>

                <span class="font-semibold">
                    Privacy Policy
                </span>

            </div>

            <span class="text-slate-400">›</span>

        </a>

    </div>

    {{-- Logout --}}
    <form
        method="POST"
        action="{{ route('logout') }}">

        @csrf

        <button
            class="flex w-full items-center justify-center gap-3 rounded-2xl bg-red-500 py-4 text-base font-black text-white shadow transition hover:bg-red-600">

            <x-heroicon-s-arrow-right-on-rectangle class="h-6 w-6"/>

            Logout

        </button>

    </form>

    <div class="pb-6 text-center text-xs text-slate-400">

        Version {{ config('app.version', '1.0.0') }}

    </div>

</div>