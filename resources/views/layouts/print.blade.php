<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Print')</title>

    {{-- Include only CSS, no JS to avoid Echo/Pusher on print pages --}}
    @vite(['resources/css/app.css'])

    @stack('styles')
    <style>
      html, body { background: #fff; }
      @media print { html, body { background: #fff; } }
    </style>
</head>
<body class="font-sans antialiased">
    <main class="py-2">
        @yield('content')
    </main>

    {{-- Allow page-specific scripts if needed --}}
    @stack('scripts')
</body>
</html>
