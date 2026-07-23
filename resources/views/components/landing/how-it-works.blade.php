<section
    id="como-funciona"
    class="relative overflow-hidden rounded-[3rem] bg-slate-950 px-5 py-14 text-white"
>
    <div
        class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-[#E52471]/30 blur-3xl"
    ></div>

    <div
        class="absolute -bottom-28 -left-28 h-72 w-72 rounded-full bg-amber-400/10 blur-3xl"
    ></div>

    <div class="relative">
        <p class="text-xs font-black uppercase tracking-[.24em] text-[#FF7CAE]">
            Fácil y rápido
        </p>

        <h2 class="text-balance mt-3 text-4xl font-black leading-[1.02] tracking-tight">
            De tu mercado local
            <span class="text-[#FFD166]">hasta tu puerta.</span>
        </h2>

        <div class="mt-10 space-y-4">

            @php
                $steps = [
                    [
                        'number' => '01',
                        'icon' => '🔍',
                        'title' => 'Explora negocios',
                        'description' => 'Encuentra tiendas, restaurantes y comercios del Pitillal.',
                    ],
                    [
                        'number' => '02',
                        'icon' => '🛍️',
                        'title' => 'Arma tu pedido',
                        'description' => 'Elige tus productos y revisa el total antes de ordenar.',
                    ],
                    [
                        'number' => '03',
                        'icon' => '🛵',
                        'title' => 'Recíbelo',
                        'description' => 'Un repartidor local llevará tu compra hasta tu domicilio.',
                    ],
                ];
            @endphp

            @foreach($steps as $step)
                <article
                    class="relative overflow-hidden rounded-4xl border border-white/10 bg-white/[.07] p-5 backdrop-blur-xl"
                >
                    <span
                        class="absolute right-4 top-2 text-5xl font-black text-white/5"
                    >
                        {{ $step['number'] }}
                    </span>

                    <div class="flex items-start gap-4">
                        <div
                            class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-[#E52471] text-2xl shadow-lg shadow-pink-950/30"
                        >
                            {{ $step['icon'] }}
                        </div>

                        <div class="pt-1">
                            <p class="text-[10px] font-black uppercase tracking-[.22em] text-[#FF8AB8]">
                                Paso {{ $step['number'] }}
                            </p>

                            <h3 class="mt-1 text-lg font-black">
                                {{ $step['title'] }}
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-white/60">
                                {{ $step['description'] }}
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach

        </div>
    </div>
</section>