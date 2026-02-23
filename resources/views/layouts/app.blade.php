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

<body class="bg-gray-900 text-white">

    <div class="flex">
        @auth
            @include('layouts.sidebar')
        @endauth

        <div id="main-content" class="main-content flex-1 min-h-screen min-w-0">
        {{-- Header --}}
        @auth
    @include('layouts.navigation')
@endauth

            <main class="w-full app-main pt-24">
               {{ $slot }}
           </main>
        </div>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>