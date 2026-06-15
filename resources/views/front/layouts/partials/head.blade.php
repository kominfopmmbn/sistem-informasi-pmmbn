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

    .page-transition-logo-wrap {
        position: relative;
        width: 140px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-transition-logo {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        position: relative;
        z-index: 1;
    }

    .spinner-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 4px solid transparent;
        border-top-color: #8B1A1A;
        border-right-color: #C8960C;
    }

    .spinner-ring-outer {
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        border: 2px solid transparent;
        border-top-color: rgba(139, 26, 26, 0.25);
        border-bottom-color: rgba(200, 150, 12, 0.25);
    }

    .page-transition-overlay.is-animating .spinner-ring {
        animation: spin 1.2s linear infinite;
    }

    .page-transition-overlay.is-animating .spinner-ring-outer {
        animation: spin-reverse 2s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes spin-reverse {
        to { transform: rotate(-360deg); }
    }
</style>

    @stack('styles')
</head>
