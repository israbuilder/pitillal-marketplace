<footer class="safe-bottom bg-slate-950 px-5 pb-8 pt-12 text-white">

    <div class="flex items-start justify-between gap-5">
        <div>
            <div class="flex items-center gap-3">
                <span
                    class="grid h-12 w-12 place-items-center rounded-2xl bg-[#E52471] text-2xl"
                >
                    🛍️
                </span>

                <div>
                    <p class="font-black">
                        El Pitillal
                    </p>

                    <p class="text-[10px] font-bold uppercase tracking-[.22em] text-white/40">
                        Marketplace
                    </p>
                </div>
            </div>

            <p class="mt-4 max-w-57.5 text-xs leading-5 text-white/45">
                Mercado local y entregas a domicilio en Puerto Vallarta,
                Jalisco, México.
            </p>
        </div>
    </div>

    <div class="mt-9 grid grid-cols-2 gap-3">

        <a
            href="{{ route('customer.home') }}"
            class="rounded-2xl border border-white/10 bg-white/6 p-4"
        >
            <p class="text-xs font-black">
                Comprar
            </p>

            <p class="mt-1 text-[10px] text-white/40">
                Explorar productos
            </p>
        </a>

        <a
            href="{{ route('register') }}"
            class="rounded-2xl border border-white/10 bg-white/6 p-4"
        >
            <p class="text-xs font-black">
                Vender
            </p>

            <p class="mt-1 text-[10px] text-white/40">
                Registrar negocio
            </p>
        </a>

    </div>

    <div
        class="mt-9 flex items-center justify-between border-t border-white/10 pt-5"
    >
        <p class="text-[10px] text-white/35">
            © {{ now()->year }} El Pitillal Marketplace
        </p>

        <p class="text-[10px] font-bold text-white/35">
            Puerto Vallarta 🇲🇽
        </p>
    </div>

</footer>