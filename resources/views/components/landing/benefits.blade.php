<section class="px-5 py-16">

    <div class="rounded-[2.7rem] bg-[#FFF0F6] p-5">

        <div class="rounded-[2.2rem] bg-white p-5 shadow-sm">
            <span
                class="inline-flex rounded-full bg-[#E52471] px-3 py-1.5 text-[10px] font-black uppercase tracking-[.2em] text-white"
            >
                Más que entregas
            </span>

            <h2 class="text-balance mt-4 text-3xl font-black leading-tight tracking-tight">
                Una plataforma hecha para
                <span class="text-[#E52471]">
                    nuestra comunidad.
                </span>
            </h2>

            <p class="mt-4 text-sm leading-6 text-slate-500">
                Conectamos clientes, negocios y repartidores locales en una sola
                experiencia rápida, simple y cercana.
            </p>

            <div class="mt-7 space-y-3">

                @foreach([
                    [
                        'icon' => '🏪',
                        'title' => 'Apoya negocios locales',
                        'text' => 'Tus compras ayudan directamente a comercios de la zona.',
                    ],
                    [
                        'icon' => '📍',
                        'title' => 'Productos cercanos',
                        'text' => 'Encuentra opciones disponibles cerca de tu ubicación.',
                    ],
                    [
                        'icon' => '🛵',
                        'title' => 'Repartidores de la comunidad',
                        'text' => 'Generamos oportunidades para conductores y motociclistas.',
                    ],
                    [
                        'icon' => '💬',
                        'title' => 'Compra fácil',
                        'text' => 'Una experiencia sencilla desde tu celular.',
                    ],
                ] as $benefit)
                    <article class="flex items-start gap-4 rounded-2xl bg-slate-50 p-4">
                        <span
                            class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-2xl shadow-sm"
                        >
                            {{ $benefit['icon'] }}
                        </span>

                        <div>
                            <h3 class="text-sm font-black text-slate-950">
                                {{ $benefit['title'] }}
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                {{ $benefit['text'] }}
                            </p>
                        </div>
                    </article>
                @endforeach

            </div>
        </div>

    </div>

</section>