<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Marketplace' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @livewireStyles
</head>
<body class="bg-slate-100 text-slate-950 antialiased">
    <div class="mx-auto min-h-screen max-w-md bg-slate-50 shadow-2xl">
        <header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur">
            <div class="flex items-center justify-between">
                <a href="{{ url()->previous() }}" class="grid h-10 w-10 place-items-center rounded-full bg-slate-100" aria-label="Regresar">←</a>
                <h1 class="text-base font-black">{{ $title ?? 'Marketplace' }}</h1>
                <div class="h-10 w-10">
                         <form method="POST" action="{{ route('logout') }}" > 
                        @csrf 
                        <button class="w-full px-4 py-3 text-left
                            text-red-600 hover:bg-red-50" > 
                           <x-heroicon-s-arrow-right-on-rectangle class="h-6 w-6" />
                        </button>
                     </form>
                </div>
                
            </div>
        </header>

        <main class="px-4 py-5">{{ $slot }}</main>

        @auth
            <nav class="fixed inset-x-0 bottom-0 z-50 mx-auto grid max-w-md grid-cols-4 border-t border-slate-200 bg-white px-2 pb-[calc(.5rem+env(safe-area-inset-bottom))] pt-2">
                @php
                        ($role = auth()->user()->role ?? 'customer');
                        $menuWallet = auth()->user()?->driverWallet;
                @endphp
                @if($role === 'business')
                    <a wire:navigate href="{{ route('business.dashboard') }}" class="rounded-xl p-2 text-center text-xs font-bold">Inicio</a>
                    <a wire:navigate href="{{ route('business.orders') }}" class="rounded-xl p-2 text-center text-xs font-bold">Pedidos</a>
                    <a wire:navigate href="{{ route('business.products') }}" class="rounded-xl p-2 text-center text-xs font-bold">Productos</a>
                    <a wire:navigate href="{{ route('business.profile') }}" class="rounded-xl p-2 text-center text-xs font-bold">Negocio</a>
                @elseif($role === 'driver')
                    <a wire:navigate href="{{ route('driver.dashboard') }}" class="rounded-xl p-2 text-center text-xs font-bold">Trabajo</a>
                    <a wire:navigate href="{{ route('driver.orders') }}" class="rounded-xl p-2 text-center text-xs font-bold">Historial</a>
                    <a wire:navigate href="{{ route('driver.profile') }}" class="rounded-xl p-2 text-center text-xs font-bold">Perfil</a>
                    
                    <a wire:navigate href="{{ route('wallet.index') }}" class="rounded-xl p-2 text-center text-xs font-bold">
                        Wallet
                        <span class="block text-[10px] text-emerald-600">
                            {{ $menuWallet?->formatted_balance ?? '$0.00' }}
                        </span>
                    </a>

                @else
                    <a wire:navigate href="{{ route('customer.home') }}" class="rounded-xl p-2 text-center text-xs font-bold">Inicio</a>
                    <a wire:navigate href="{{ route('customer.orders') }}" class="rounded-xl p-2 text-center text-xs font-bold">Pedidos</a>
                    <a wire:navigate href="{{ route('customer.cart') }}" class="rounded-xl p-2 text-center text-xs font-bold">Carrito</a>
                    <a wire:navigate href="{{ route('customer.profile') }}" class="rounded-xl p-2 text-center text-xs font-bold">Perfil</a>
                @endif
            </nav>
        @endauth
    </div>
    @livewireScripts
</body>
</html>
