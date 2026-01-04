<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Architect AI') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <script src="https://unpkg.com/alpinejs" defer></script>
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen bg-background">
            <!-- Sidebar -->
            <aside class="w-64 bg-sidebar text-sidebar-foreground flex flex-col">
                <div class="p-6 border-b border-sidebar-border">
                    <a href="/dashboard" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-sidebar-primary rounded-lg flex items-center justify-center">
                            <i data-lucide="brain" class="w-5 h-5 text-sidebar-primary-foreground"></i>
                        </div>
                        <div>
                            <h1 class="text-sm font-semibold">AI ARCHITECT</h1>
                            <p class="text-xs text-sidebar-foreground/60">Business Operations Hub</p>
                        </div>
                    </a>
                </div>

                <nav class="flex-1 p-4">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-sidebar-foreground/50 px-3 mb-3">MAIN MENU</p>
                        
                        <a href="/dashboard" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            Dashboard
                        </a>

                        @if(auth()->user()->tenant->type === 'agency')
                        <a href="{{ route('sub-accounts.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('settings/sub-accounts*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            Sub-Accounts
                        </a>
                        @endif

                        <a href="{{ route('users.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('settings/users*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            Team Management
                        </a>

                        @if(auth()->user()->tenant->type === 'agency')
                        <a href="{{ route('policies.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('settings/policies*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            Access Policies
                        </a>
                        @endif
                        
                        <a href="/research-engine" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('research-engine') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="brain" class="w-4 h-4"></i>
                            Research Engine
                        </a>

                        <a href="/content-creator" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('content-creator') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                            Content Creator
                        </a>

                        <a href="/social-planner" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('social-planner') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            Social Planner
                        </a>

                        <a href="/knowledge-base" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('knowledge-base') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="database" class="w-4 h-4"></i>
                            Knowledge Base
                        </a>

                        <a href="/report-builder" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('report-builder') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                            Report Builder
                        </a>

                        <a href="/documents" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('documents') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Documents
                        </a>

                        <a href="/analytics" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('analytics') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            Analytics
                        </a>
                    </div>

                    <div class="mt-8 space-y-1">
                        <p class="text-xs font-semibold text-sidebar-foreground/50 px-3 mb-3">HELP & SUPPORT</p>
                        <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground transition-colors">
                            <i data-lucide="help-circle" class="w-4 h-4"></i>
                            Help & Center
                        </button>
                        <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground transition-colors">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            Settings
                        </button>
                    </div>
                </nav>

                <div class="p-4 border-t border-sidebar-border">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Log Out
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col overflow-hidden">
                <header class="bg-card border-b border-border px-8 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Welcome Back, AI Architect</h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input type="search" placeholder="Search anything" class="pl-9 w-80 bg-background flex h-10 w-full rounded-md border border-input px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                        </div>
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </button>
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                        </button>
                        <div class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-full">
                            <div class="flex h-full w-full items-center justify-center rounded-full bg-muted">AA</div>
                        </div>
                    </div>
                </header>

                <div class="flex-1 overflow-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>
