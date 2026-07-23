<section class="px-5 pb-16">

    <div class="grid gap-5 rounded-[3rem] bg-[#F6F7FB] p-6">

        <div class="relative h-64 overflow-hidden rounded-[2.3rem] bg-slate-950">

            <div
                class="absolute left-1/2 top-1/2 h-44 w-44 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#E52471]/30"
            ></div>

            <div
                class="animate-ring absolute left-1/2 top-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-[#E52471]"
            ></div>

            <div
                class="absolute left-6 top-6 rounded-full bg-white/10 px-3 py-2 text-[10px] font-black uppercase tracking-[.2em] text-white backdrop-blur"
            >
                Entregas locales
            </div>

            <div
                class="animate-float absolute left-1/2 top-1/2 grid h-28 w-28 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-[#E52471] text-6xl shadow-2xl shadow-pink-950/50"
            >
                🛵
            </div>

            <div
                class="absolute bottom-5 left-5 right-5 flex items-center justify-between rounded-2xl bg-white p-3 text-slate-950 shadow-xl"
            >
                <div>
                    <p class="text-[10px] font-bold text-slate-400">
                        Próxima entrega
                    </p>

                    <p class="text-sm font-black">
                        El Pitillal Centro
                    </p>
                </div>

                <span
                    class="rounded-full bg-emerald-100 px-3 py-1.5 text-[10px] font-black text-emerald-700"
                >
                    Disponible
                </span>
            </div>
        </div>

        <div>
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#E52471]">
                Para repartidores
            </p>

            <h2 class="text-balance mt-3 text-3xl font-black leading-tight tracking-tight">
                Genera ingresos entregando en tu ciudad.
            </h2>

            <p class="mt-4 text-sm leading-6 text-slate-500">
                Recibe oportunidades de entrega, elige tus recorridos
                y trabaja dentro de tu zona.
            </p>

            <a
                href="{{ route('register') }}"
                class="mt-6 flex min-h-14 items-center justify-center rounded-2xl bg-slate-950 px-5 text-sm font-black text-white shadow-xl transition active:scale-[.98]"
            >
                Quiero ser repartidor
            </a>
        </div>

    </div>

</section>