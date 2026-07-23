<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >

    <meta
        name="description"
        content="El Pitillal Marketplace. Compra productos de negocios locales y recíbelos a domicilio en Puerto Vallarta, Jalisco."
    >

    <meta name="theme-color" content="#E52471">

    <title>
        {{ $title ?? 'El Pitillal Marketplace' }}
    </title>

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
          content="{{ asset('assets/images/share2.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
          content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description"
          content="Compra productos locales y recibe entregas rápidas con Impulsando Latinos Marketplace.">
    <meta name="twitter:image"
          content="{{ asset('assets/images/share.jpg') }}">
    <link
        rel="icon"
        type="image/png"
        href="{{ asset('asstes/images/favicon.png') }}"
    >

    <link
        rel="apple-touch-icon"
        href="{{ asset('assets/images/apple-touch-icon.png') }}"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --market-pink: #E52471;
            --market-pink-dark: #B81456;
            --market-pink-light: #FFF0F6;
            --market-yellow: #FFD166;
            --market-green: #1FA97A;
            --market-dark: #17121A;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(229, 36, 113, .09), transparent 28rem),
                #fff;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        [x-cloak] {
            display: none !important;
        }

        .safe-top {
            padding-top: env(safe-area-inset-top);
        }

        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }

        .market-shadow {
            box-shadow:
                0 20px 50px rgba(40, 22, 32, .10),
                0 4px 12px rgba(40, 22, 32, .05);
        }

        .pink-shadow {
            box-shadow:
                0 18px 40px rgba(229, 36, 113, .30),
                0 6px 16px rgba(229, 36, 113, .18);
        }

        .text-balance {
            text-wrap: balance;
        }

        .animate-float {
            animation: floating 4s ease-in-out infinite;
        }

        .animate-float-delayed {
            animation: floating 4.8s ease-in-out infinite 1s;
        }

        .animate-soft-pulse {
            animation: softPulse 2.8s ease-in-out infinite;
        }

        .animate-scroll-products {
            animation: scrollProducts 20s linear infinite;
        }

        .animate-ring {
            animation: ring 2.4s ease-out infinite;
        }

        .hero-orb-one {
            animation: orbOne 8s ease-in-out infinite alternate;
        }

        .hero-orb-two {
            animation: orbTwo 10s ease-in-out infinite alternate;
        }

        @keyframes floating {
            0%, 100% {
                transform: translateY(0) rotate(-1deg);
            }

            50% {
                transform: translateY(-12px) rotate(1deg);
            }
        }

        @keyframes softPulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.04);
                opacity: .92;
            }
        }

        @keyframes scrollProducts {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        @keyframes ring {
            0% {
                transform: scale(.8);
                opacity: .55;
            }

            100% {
                transform: scale(1.65);
                opacity: 0;
            }
        }

        @keyframes orbOne {
            from {
                transform: translate3d(-10px, -4px, 0) scale(1);
            }

            to {
                transform: translate3d(18px, 22px, 0) scale(1.15);
            }
        }

        @keyframes orbTwo {
            from {
                transform: translate3d(10px, 10px, 0) scale(1.05);
            }

            to {
                transform: translate3d(-14px, -18px, 0) scale(.94);
            }
        }
    </style>

    {{ $head ?? '' }}
</head>

<body class="min-h-screen overflow-x-hidden bg-white font-sans text-slate-950">

    <div
        class="mx-auto min-h-screen w-full max-w-md overflow-hidden bg-white shadow-2xl shadow-slate-200/60"
    >
        {{ $slot }}
    </div>

</body>
</html>