<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="theme-color" content="#ffffff">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">

        <title>{{ config('app.name', 'ColiDecor') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts dhe Styles -->
        @vite([
            'resources/css/app.css',
            'resources/js/app.js'
        ])
        
        <!-- Touch-friendly styles -->
        <link href="{{ asset('css/touch-styles.css') }}" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            const userIdMeta = document.querySelector('meta[name="user-id"]');
            const userIdContent = userIdMeta ? userIdMeta.getAttribute('content') : null;
            window.userId = userIdContent ? parseInt(userIdContent, 10) : null;
        </script>
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased h-full bg-gray-100 disable-pull-refresh touch-manipulation">
        <x-touch-interface />
        <!-- Tap highlight color fix for mobile -->
        <style>
            * {
                -webkit-tap-highlight-color: rgba(0,0,0,0);
            }
            
            /* Prevent pull-to-refresh on mobile */
            .disable-pull-refresh {
                overscroll-behavior-y: contain;
            }
            
            /* Improve form elements on touch devices */
            input, select, textarea, button {
                font-size: 16px; /* Prevents zoom on focus in iOS */
            }
        </style>
        <!-- Notifications Container -->
        <div id="notifications-container" class="fixed top-4 right-4 z-50 space-y-4 pointer-events-none"></div>
        
        <div class="min-h-screen bg-gray-100 touch-scroll">
            @include('layouts.navigation')

            <!-- Session Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Notification System -->
            <x-notification-system />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-300 touch-manipulation">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="pb-safe transition-colors duration-300">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Stack for scripts -->
        @stack('scripts')
    </body>
</html>
