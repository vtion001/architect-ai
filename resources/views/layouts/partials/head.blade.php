{{-- 
    App Layout Head Section (Performance Optimized)
    
    Optimizations applied:
    1. Preconnect to critical origins
    2. Optimized favicon with proper sizing
    3. Critical CSS inline for faster FCP
    4. Font loading with display=swap
    5. Alpine.js loaded with defer (non-blocking)
    6. Lucide icons via Vite bundle (no CDN duplicate)
--}}
@php
    $tenant = app(\App\Models\Tenant::class);
    $brandColor = $tenant->metadata['primary_color'] ?? '#00F2FF';
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ArchitGrid') }}</title>

    {{-- ================================================================
         Resource Hints (Preconnect)
    ================================================================ --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://res.cloudinary.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    {{-- ================================================================
         Favicon - Optimized sizes
    ================================================================ --}}
    <link rel="icon" type="image/png" sizes="32x32" 
          href="https://res.cloudinary.com/dbviya1rj/image/upload/w_32,h_32,c_fill,f_auto,q_auto/v1767554289/xe54y8zsvhursjrpbnvm.png">
    <link rel="apple-touch-icon" sizes="180x180"
          href="https://res.cloudinary.com/dbviya1rj/image/upload/w_180,h_180,c_fill,f_auto,q_auto/v1767554289/xe54y8zsvhursjrpbnvm.png">

    {{-- ================================================================
         Critical CSS (Inline for faster FCP)
    ================================================================ --}}
    <style>
        :root {
            --primary: {{ $brandColor }};
            --sidebar-primary: {{ $brandColor }};
            --sidebar-accent-foreground: {{ $brandColor }};
            --sidebar-ring: {{ $brandColor }};
            --ring: {{ $brandColor }};
        }
        [x-cloak] { display: none !important; }
        html, body { 
            margin: 0; 
            padding: 0; 
            min-height: 100vh;
            font-family: 'Figtree', system-ui, -apple-system, sans-serif;
        }
    </style>

    {{-- ================================================================
         Fonts
    ================================================================ --}}
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    {{-- ================================================================
         Main App Styles & Scripts (Vite)
         Lucide is bundled here - no CDN needed
    ================================================================ --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ================================================================
         Alpine.js (CDN with defer - non-blocking)
    ================================================================ --}}
    <script src="https://unpkg.com/alpinejs@3.14.3/dist/cdn.min.js" defer></script>

    {{-- Page-specific head content --}}
    @stack('head')
</head>
