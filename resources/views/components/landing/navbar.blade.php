<header class="safe-top absolute inset-x-0 top-0 z-50">
    <nav class="mx-auto flex max-w-md items-center justify-between px-5 py-4">

        <a
            href="#inicio"
            class="flex items-center gap-2"
            aria-label="Ir al inicio"
        >
            <span
                class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-xl shadow-lg shadow-pink-950/10"
            >
                🛍️
            </span>

            <span class="leading-tight text-white">
                <span class="block text-sm font-black tracking-tight">
                    El Pitillal
                </span>

                <span class="block text-[10px] font-bold uppercase tracking-[.24em] text-white/70">
                    Marketplace
                </span>
            </span>
        </a>

        <a
            href="{{ route('login') }}"
            class="rounded-full border border-white/25 bg-white/15 px-4 py-2.5 text-xs font-black text-white shadow-sm backdrop-blur-xl transition active:scale-95"
        >
            Ingresar
        </a>

    </nav>
</header>