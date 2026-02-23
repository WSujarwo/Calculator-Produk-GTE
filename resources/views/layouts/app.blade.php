<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}?v={{ filemtime(public_path('css/sidebar.css')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-900 text-white antialiased">

    <div class="flex min-h-screen">
        {{-- 1. SIDEBAR --}}
        @auth
            @include('layouts.sidebar')
        @endauth

        {{-- 2. WRAPPER UNTUK NAV & KONTEN --}}
        <div id="main-content" class="flex-1 flex flex-col min-w-0 transition-all duration-300">
            
            {{-- TOP NAVIGATION --}}
            @auth
                <div class="sticky top-0 z-40">
                    @include('layouts.navigation')
                </div>
            @endauth

            {{-- HEADER (Judul Halaman) --}}
            @if (isset($header))
                <header class="bg-white border-b border-gray-200">
                    <div class="py-4">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- MAIN CONTENT --}}
            <main class="flex-1 bg-gray-50 text-gray-900">
                <div class="py-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>