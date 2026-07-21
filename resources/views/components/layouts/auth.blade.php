<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-slate-100 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-dvh w-full max-w-md flex-col bg-white shadow-xl">
        <div class="flex-1 px-6 pb-[max(2rem,env(safe-area-inset-bottom))] pt-[max(2rem,env(safe-area-inset-top))]">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
</body>
</html>
