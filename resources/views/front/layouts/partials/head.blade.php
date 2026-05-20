<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PMMBN</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/pmmbn.ico') }}" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets-front-pages/css/style.css') }}">
    <style>
        .page-transition-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 9999;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .page-transition-overlay.is-visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .page-transition-logo {
            width: 140px;
            max-width: 40vw;
            transform-origin: center;
        }

        .page-transition-overlay.is-animating .page-transition-logo {
            animation: pmmbn-logo-bounce 1.3s ease;
        }

        @keyframes pmmbn-logo-bounce {
            0% {
                transform: translateY(0) scale(0.92);
            }

            20% {
                transform: translateY(-12px) scale(1.02);
            }

            40% {
                transform: translateY(0) scale(0.98);
            }

            60% {
                transform: translateY(-6px) scale(1);
            }

            80% {
                transform: translateY(0) scale(0.99);
            }

            100% {
                transform: translateY(0) scale(1);
            }
        }
    </style>

    @stack('styles')
</head>
