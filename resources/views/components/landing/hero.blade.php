<section
    id="inicio"
    class="relative isolate min-h-195 overflow-hidden rounded-b-[3rem] bg-[#E52471] px-5 pb-14 pt-28 text-white"
>
    {{-- Luces decorativas --}}
    <div
        class="hero-orb-one absolute -left-24 top-10 h-64 w-64 rounded-full bg-white/15 blur-3xl"
    ></div>

    <div
        class="hero-orb-two absolute -right-28 top-52 h-72 w-72 rounded-full bg-amber-300/30 blur-3xl"
    ></div>

    <div
        class="absolute inset-x-0 bottom-0 h-56 bg-linear-to-t from-[#BE1558]/70 to-transparent"
    ></div>

    <div class="relative z-10">

        <div
            class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-3 py-2 text-xs font-bold text-white shadow-sm backdrop-blur-xl"
        >
            <span class="relative flex h-2.5 w-2.5">
                <span
                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-75"
                ></span>

                <span
                    class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-300"
                ></span>
            </span>

            Compras locales en Puerto Vallarta
        </div>

        <h1
            class="text-balance mt-7 text-[3.6rem] font-black leading-[.91] tracking-[-.065em]"
        >
            El Pitillal
            <span class="block text-[#FFD166]">
                a domicilio.
            </span>
        </h1>

        <p
            class="text-balance mt-6 max-w-sm text-[1.05rem] font-medium leading-7 text-white/85"
        >
            Descubre productos de negocios locales, compra desde tu celular
            y recíbelos directamente en tu puerta.
        </p>

        <div class="mt-8 grid grid-cols-[1fr_auto] gap-3">
            <a
                href="{{ route('customer.home') }}"
                class="pink-shadow flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-black text-[#C7195D] transition active:scale-[.97]"
            >
                Explorar mercado

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.4"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 12h14m-6-6 6 6-6 6"
                    />
                </svg>
            </a>

            <a
                href="#como-funciona"
                class="grid h-14 w-14 place-items-center rounded-2xl border border-white/20 bg-white/15 text-white backdrop-blur-xl transition active:scale-95"
                aria-label="Conocer cómo funciona"
            >
                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m19 9-7 7-7-7"
                    />
                </svg>
            </a>
        </div>

        {{-- Mockup flotante --}}
        <div class="relative mt-12 h-78.75">

            <div
                class="animate-float absolute left-1/2 top-0 w-2.4rem -translate-x-1/2 rotate-3 rounded-[2.4rem] border-[7px] border-slate-950 bg-white p-3 text-slate-950 shadow-2xl shadow-pink-950/40"
            >
                <div class="mx-auto mb-3 h-1.5 w-16 rounded-full bg-slate-200"></div>

                <div class="rounded-[1.8rem] bg-slate-50 p-3">

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                Entrega en
                            </p>

                            <p class="text-xs font-black">
                                El Pitillal, Vallarta
                            </p>
                        </div>

                        <span
                            class="grid h-8 w-8 place-items-center rounded-full bg-[#FFE6F0]"
                        >
                            📍
                        </span>
                    </div>

                    <div class="mt-4 rounded-2xl bg-[#E52471] p-4 text-white">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-white/70">
                            Mercado local
                        </p>

                        <p class="mt-1 text-lg font-black leading-5">
                            Todo cerca de ti
                        </p>

                        <div class="mt-3 h-1.5 rounded-full bg-white/20">
                            <div class="h-1.5 w-2/3 rounded-full bg-[#FFD166]"></div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2">
                        @foreach([
                            ['icon' => '🥑', 'name' => 'Frutas'],
                            ['icon' => '🥩', 'name' => 'Carnes'],
                            ['icon' => '🌮', 'name' => 'Comida'],
                        ] as $category)
                            <div class="rounded-2xl bg-white p-2 text-center shadow-sm">
                                <div class="text-2xl">
                                    {{ $category['icon'] }}
                                </div>

                                <p class="mt-1 text-[9px] font-black">
                                    {{ $category['name'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-center gap-3 rounded-2xl bg-white p-2 shadow-sm">
                        <div
                            class="grid h-11 w-11 place-items-center rounded-xl bg-amber-100 text-2xl"
                        >
                            🛒
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[10px] font-black">
                                Tu pedido está en camino
                            </p>

                            <p class="mt-0.5 text-[9px] text-slate-400">
                                Llega aproximadamente en 25 min
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Tarjeta flotante izquierda --}}
            <div
                class="animate-float-delayed absolute left-0 top-16 rounded-2xl border border-white/30 bg-white/95 p-3 text-slate-950 shadow-xl"
            >
                <div class="flex items-center gap-2">
                    <span
                        class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-100 text-lg"
                    >
                        ⚡
                    </span>

                    <div>
                        <p class="text-[9px] font-bold text-slate-400">
                            Entrega rápida
                        </p>

                        <p class="text-xs font-black">
                            25-40 min
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tarjeta flotante derecha --}}
            <div
                class="animate-soft-pulse absolute right-0 top-44 rounded-2xl border border-white/25 bg-slate-950 px-3 py-2.5 shadow-xl"
            >
                <p class="text-[9px] font-bold uppercase tracking-widest text-white/50">
                    Negocio local
                </p>

                <p class="mt-1 text-xs font-black text-white">
                    Compra y apoya 💗
                </p>
            </div>

        </div>

    </div>
</section>