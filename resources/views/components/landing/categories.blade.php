<section class="px-5 py-14">

    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#E52471]">
                Explora
            </p>

            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                Lo que necesitas,
                <span class="text-[#E52471]">cerca de ti.</span>
            </h2>
        </div>

        <span
            class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-[#FFF0F6] text-xl"
        >
            📍
        </span>
    </div>

    <div class="mt-8 grid grid-cols-2 gap-3">

        @php
            $categories = [
                [
                    'name' => 'Frutas y verduras',
                    'description' => 'Fresco todos los días',
                    'icon' => '🥑',
                    'background' => 'bg-emerald-50',
                ],
                [
                    'name' => 'Comida preparada',
                    'description' => 'Sabor de tu colonia',
                    'icon' => '🌮',
                    'background' => 'bg-amber-50',
                ],
                [
                    'name' => 'Carnes y mariscos',
                    'description' => 'Calidad local',
                    'icon' => '🥩',
                    'background' => 'bg-rose-50',
                ],
                [
                    'name' => 'Abarrotes',
                    'description' => 'Todo para tu hogar',
                    'icon' => '🛒',
                    'background' => 'bg-sky-50',
                ],
                [
                    'name' => 'Pan y postres',
                    'description' => 'Recién preparados',
                    'icon' => '🥐',
                    'background' => 'bg-orange-50',
                ],
                [
                    'name' => 'Farmacia y cuidado',
                    'description' => 'Bienestar a domicilio',
                    'icon' => '💊',
                    'background' => 'bg-violet-50',
                ],
            ];
        @endphp

        @foreach($categories as $category)
            <a
                href="{{ route('customer.home') }}"
                class="market-shadow group relative min-h-44 overflow-hidden rounded-2rem border border-slate-100 bg-white p-4 transition active:scale-[.97]"
            >
                <div
                    class="absolute -right-6 -top-5 h-24 w-24 rounded-full {{ $category['background'] }}"
                ></div>

                <div
                    class="relative grid h-16 w-16 place-items-center rounded-2xl {{ $category['background'] }} text-4xl transition duration-300 group-hover:rotate-6 group-hover:scale-110"
                >
                    {{ $category['icon'] }}
                </div>

                <h3 class="relative mt-5 text-base font-black leading-5 text-slate-950">
                    {{ $category['name'] }}
                </h3>

                <p class="relative mt-1 text-xs font-medium text-slate-500">
                    {{ $category['description'] }}
                </p>

                <span
                    class="absolute bottom-4 right-4 grid h-8 w-8 place-items-center rounded-full bg-slate-950 text-white"
                >
                    <svg
                        class="h-4 w-4"
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
                </span>
            </a>
        @endforeach

    </div>

</section>