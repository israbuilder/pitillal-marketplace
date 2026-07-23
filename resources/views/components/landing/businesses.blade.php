<section
    id="negocios"
    class="relative overflow-hidden px-5 pb-16"
>
    <div
        class="relative overflow-hidden rounded-[3rem] bg-linear-to-br from-[#E52471] via-[#D31C68] to-[#A50F4B] p-6 text-white shadow-2xl shadow-pink-200"
    >
        <div
            class="absolute -right-12 -top-12 h-44 w-44 rounded-full border-30 border-white/6"
        ></div>

        <div
            class="absolute -bottom-16 -left-12 h-48 w-48 rounded-full bg-amber-300/20 blur-2xl"
        ></div>

        <div class="relative">
            <span
                class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-3 py-2 text-[10px] font-black uppercase tracking-[.2em]"
            >
                <span class="h-2 w-2 rounded-full bg-[#FFD166]"></span>
                Para comercios
            </span>

            <h2 class="text-balance mt-5 text-4xl font-black leading-[1.02] tracking-tight">
                Lleva tu negocio
                <span class="text-[#FFD166]">
                    a más clientes.
                </span>
            </h2>

            <p class="mt-4 text-sm leading-6 text-white/75">
                Publica tus productos, recibe pedidos y comienza a vender
                en línea dentro de tu propia comunidad.
            </p>

            <div class="mt-7 grid grid-cols-2 gap-3">

                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-2xl font-black text-[#FFD166]">
                        24/7
                    </p>

                    <p class="mt-1 text-xs font-bold text-white/70">
                        Tu catálogo disponible
                    </p>
                </div>

                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-2xl font-black text-[#FFD166]">
                        Local
                    </p>

                    <p class="mt-1 text-xs font-bold text-white/70">
                        Clientes de tu zona
                    </p>
                </div>

            </div>

            <a
                href="{{ route('register') }}"
                class="mt-7 flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-black text-[#C7195D] shadow-xl transition active:scale-[.98]"
            >
                Registrar mi negocio

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
        </div>
    </div>
</section>