<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin Ops - {{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <script src="https://unpkg.com/alpinejs" defer></script>
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-200">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col">
                <div class="p-6 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-900/20">
                            <i data-lucide="terminal" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-sm font-black tracking-tight text-white uppercase">Platform Ops</h1>
                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest animate-pulse">Developer Mode</p>
                        </div>
                    </div>
                </div>

                <nav class="flex-1 p-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                        Ops Dashboard
                    </a>

                    <a href="{{ route('god-view.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('god-view.*') ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        Master God View
                    </a>
                    
                    <a href="{{ route('admin.tenants.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.tenants.*') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="server" class="w-4 h-4"></i>
                        Tenant Explorer
                    </a>

                    <a href="{{ route('admin.audit.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.audit.*') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                        Global Audit
                    </a>

                    <div class="pt-4 pb-2 px-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">System Tools</div>
                    
                    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all text-left">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        Policy Architect
                    </button>
                </nav>

                <div class="p-4 border-t border-slate-800 bg-slate-900/50">
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition-all">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Back to App
                    </a>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col relative overflow-hidden">
                <!-- Observability Banner -->
                @if(session('developer_observability_mode'))
                <div class="bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.3em] py-1.5 text-center shadow-lg relative z-50">
                    ⚠️ Global Observability Active - All Data Scopes Lifted ⚠️
                </div>
                @endif

                <header class="h-16 bg-slate-900 border-b border-slate-800 px-8 flex items-center justify-between shrink-0">
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">@yield('title', 'Developer Console')</h2>
                    
                    <div class="flex items-center gap-4">
                        <div class="px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Node: {{ gethostname() }}
                        </div>
                        <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-xs font-black shadow-lg">
                            {{ substr(auth()->user()->email, 0, 1) }}
                        </div>
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                    @yield('content')
                </div>
            </main>
        </div>
        <script>
            lucide.createIcons();
        </script>
    </body>
</html>
