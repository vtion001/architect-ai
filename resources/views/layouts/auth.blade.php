<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ArchitGrid') }} - Login</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600" rel="stylesheet" />

        <!-- Dynamic Branding -->
        @php
            $brandColor = '#22D3EE'; // Cyan-400
            $authLogo = 'https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png';
            $tenantName = 'ArchitGrid';
            
            try {
                $slug = request()->route('tenant_slug') ?? request('slug');
                if ($slug) {
                    $tenant = \App\Models\Tenant::where('slug', $slug)->first();
                    if ($tenant) {
                        $brandColor = $tenant->metadata['primary_color'] ?? '#22D3EE';
                        $authLogo = $tenant->metadata['logo_url'] ?? $authLogo;
                        $tenantName = $tenant->name;
                    }
                }
            } catch (\Exception $e) {
                // Fallback to defaults if DB fails (e.g. migrations pending)
            }
        @endphp
        
        <style>
            :root {
                --primary: {{ $brandColor }};
            }
            body { 
                font-family: 'Inter', sans-serif;
                background-color: #0B0F19;
            }
            h1, h2, h3, button { font-family: 'Plus Jakarta Sans', sans-serif; }
            [x-cloak] { display: none !important; }

            .glass-card {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <script src="https://unpkg.com/alpinejs" defer></script>
    </head>
    <body class="antialiased text-slate-200">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">
            <!-- Background Gradients -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-cyan-500/5 blur-[120px] rounded-full pointer-events-none -z-10"></div>
            
            <div class="mb-10 text-center">
                <a href="/" class="inline-flex flex-col items-center gap-3 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform">
                        <img src="{{ $authLogo }}" class="w-8 h-8 object-contain" alt="Logo">
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">{{ $tenantName }}</h1>
                </a>
            </div>

            @yield('content')

            <div class="mt-12 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    &copy; 2026 ArchitGrid Inc. All rights reserved.
                </p>
            </div>
        </div>
        <script>
            if (window.lucide) {
                lucide.createIcons();
            }
        </script>
    </body>
</html>