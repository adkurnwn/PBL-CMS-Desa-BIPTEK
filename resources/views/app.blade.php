<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if(isset($page['props']['meta']))
        @foreach($page['props']['meta'] as $key => $value)
            @if(is_string($value))
                <meta name="{{ $key }}" content="{{ $value }}">
            @endif
        @endforeach
    @endif
    @if(isset($page['props']['canonical']))
        <link rel="canonical" href="{{ $page['props']['canonical'] }}">
    @else
        <link rel="canonical" href="{{ url()->current() }}">
    @endif
    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('storage/favicon.png') }}?v={{ time() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icon -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />


    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
