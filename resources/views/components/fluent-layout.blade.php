@props([
    'title' => config('app.name', 'Pulpería')
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fluent Design CSS -->
    <link rel="stylesheet" href="{{ asset('css/fluent-design.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @stack('styles')
</head>
<body class="fluent-theme-light">
    <div class="fluent-layout">
        {{ $slot }}
    </div>

    @stack('scripts')
</body>
</html>

<style>
:root {
    --fluent-font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', 'Helvetica Neue', sans-serif;
    --fluent-white: #FFFFFF;
    /* ... other variables ... */
}

.fluent-layout {
    display: grid;
    grid-template-columns: auto 1fr;
    grid-template-rows: 1fr;
    height: 100vh;
    background-color: var(--fluent-background-layer-1);
}
</style>
