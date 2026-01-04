<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ArchitGrid') }} - IAM Gateway</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <script src="https://unpkg.com/alpinejs" defer></script>
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased bg-background text-foreground">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary/10 via-background to-background">
            <div class="mb-8 flex items-center gap-4">
                <div class="w-14 h-14 shrink-0 drop-shadow-2xl">
                    <img src="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png" class="w-full h-full object-contain" alt="ArchitGrid Logo">
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tighter text-foreground uppercase">ArchitGrid</h1>
                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest">Secure IAM Gateway</p>
                </div>
            </div>

            @yield('content')

            <div class="mt-8 text-center">
                <p class="text-xs text-muted-foreground font-medium">
                    &copy; 2026 ArchitGrid. All rights reserved.<br>
                    Secure multi-tenant infrastructure powered by IAM Architecture.
                </p>
            </div>
        </div>
        <script>
            lucide.createIcons();
        </script>
    </body>
</html>
