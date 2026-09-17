<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MyMusic</title>
    <meta name="description" content="MyMusic — Stream tracks, discover by mood, and enjoy synced lyrics.">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2338bdf8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Ccircle cx='12' cy='12' r='2'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Kept for legacy pages (password reset, LRC maker) that still use FA classes. React pages use Lucide. --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/react/main.jsx'])
</head>
<body>
    {{-- React mount point. Converted pages fill `react-page` + `react-props`; legacy pages use `content`. --}}
    @hasSection('react-page')
        <div id="react-root" data-page="@yield('react-page')" data-props='@yield('react-props')'></div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>
