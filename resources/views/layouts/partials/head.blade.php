{{-- App Layout Head Section --}}
@php
    $tenant = app(\App\Models\Tenant::class);
    $brandColor = $tenant->metadata['primary_color'] ?? '#00F2FF';
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ArchitGrid') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png">
    <link rel="apple-touch-icon" href="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png">

    <!-- Dynamic Branding -->
    <style>
        :root {
            --primary: {{ $brandColor }};
            --sidebar-primary: {{ $brandColor }};
            --sidebar-accent-foreground: {{ $brandColor }};
            --sidebar-ring: {{ $brandColor }};
            --ring: {{ $brandColor }};
        }
        [x-cloak] { display: none !important; }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
