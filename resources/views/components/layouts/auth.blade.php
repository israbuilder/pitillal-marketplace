<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>


    <meta name="description"
          content="Compra productos locales y recibe entregas rápidas con Impulsando Latinos Marketplace.">

    <meta name="keywords"
          content="marketplace, delivery, negocios locales, Houston, compras, restaurantes">

    <meta name="author"
          content="Impulsando Latinos">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Impulsando Latinos">
    <meta property="og:title"
          content="{{ $title ?? config('app.name') }}">
    <meta property="og:description"
          content="Compra productos locales y recibe entregas rápidas con Impulsando Latinos Marketplace.">
    <meta property="og:url"
          content="{{ url()->current() }}">
    <meta property="og:image"
          content="{{ asset('assets/images/share.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
          content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description"
          content="Compra productos locales y recibe entregas rápidas con Impulsando Latinos Marketplace.">
    <meta name="twitter:image"
           content="{{ asset('images/share2.jpg') }}">
 <link href="{{asset('assets/images/favicon.png')}}" rel="icon">
      <link href="{{asset('assets/images/apple-touch-icon.png')}}" rel="apple-touch-icon">
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
