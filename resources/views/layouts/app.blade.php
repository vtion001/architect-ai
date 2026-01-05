<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ArchitGrid') }}</title>

        <!-- Dynamic Branding -->
        @php
            $tenant = app(\App\Models\Tenant::class);
            $brandColor = $tenant->metadata['primary_color'] ?? '#00F2FF';
        @endphp
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
    <body class="font-sans antialiased" 
          x-data="{ 
            showCommandPalette: false,
            searchQuery: '',
            commands: [
                { id: 1, title: 'Go to Dashboard', shortcut: 'G D', icon: 'layout-dashboard', url: '/dashboard' },
                { id: 2, title: 'Open Content Creator', shortcut: 'G C', icon: 'pencil', url: '/content-creator' },
                { id: 3, title: 'Deep Research Engine', shortcut: 'G R', icon: 'brain', url: '/research-engine' },
                { id: 4, title: 'Social Planner', shortcut: 'G S', icon: 'calendar', url: '/social-planner' },
                { id: 5, title: 'Global Knowledge Hub', shortcut: 'G K', icon: 'database', url: '/knowledge-base' },
                { id: 6, title: 'Identity Management', shortcut: 'G U', icon: 'users', url: '/settings/users' },
                { id: 7, title: 'Open Token Treasury', shortcut: 'G T', icon: 'coins', url: '/settings?tab=billing' },
            ],
            searchResults: [],
            isSearching: false,
            get filteredCommands() {
                const staticFiltered = this.commands.filter(c => c.title.toLowerCase().includes(this.searchQuery.toLowerCase()));
                return [...staticFiltered, ...this.searchResults];
            },
            fetchResults() {
                if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                this.isSearching = true;
                fetch('/api/v1/search?q=' + encodeURIComponent(this.searchQuery))
                    .then(res => res.json())
                    .then(data => { this.searchResults = data.results || []; })
                    .finally(() => { this.isSearching = false; });
            }
          }"
          @keydown.window.ctrl.k.prevent="showCommandPalette = true"
          @keydown.window.cmd.k.prevent="showCommandPalette = true"
          @keydown.window.escape="showCommandPalette = false"
          x-init="$watch('searchQuery', () => fetchResults())">
        
        <!-- Command Palette Overlay -->
        <div x-show="showCommandPalette" x-cloak class="fixed inset-0 z-[200] flex items-start justify-center pt-[15vh] px-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div @click.away="showCommandPalette = false" class="bg-card w-full max-w-xl rounded-2xl shadow-2xl border border-border overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="p-4 border-b border-border flex items-center gap-3 bg-muted/20">
                    <template x-if="!isSearching">
                        <i data-lucide="search" class="w-5 h-5 text-primary"></i>
                    </template>
                    <template x-if="isSearching">
                        <i data-lucide="loader-2" class="w-5 h-5 text-primary animate-spin"></i>
                    </template>
                    <input type="text" x-model="searchQuery" x-ref="commandInput" autofocus
                           placeholder="Search across your grid nodes..." 
                           class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium outline-none">
                    <span class="text-[9px] font-black border border-border px-1.5 py-0.5 rounded uppercase text-muted-foreground">ESC</span>
                </div>

                <div class="max-h-96 overflow-y-auto p-2 custom-scrollbar">
                    <template x-for="cmd in filteredCommands" :key="cmd.url + cmd.title">
                        <a :href="cmd.url" class="flex items-center justify-between p-3 rounded-xl hover:bg-primary/5 group transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-muted flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                                    <i :data-lucide="cmd.icon" class="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-foreground" x-text="cmd.title"></p>
                                    <p x-show="cmd.type" class="text-[8px] font-black uppercase text-slate-500 tracking-widest" x-text="cmd.type"></p>
                                </div>
                            </div>
                            <span class="mono text-[9px] text-slate-500 font-black uppercase tracking-widest" x-text="cmd.shortcut || cmd.metadata"></span>
                        </a>
                    </template>
                    <div x-show="filteredCommands.length === 0" class="py-12 text-center text-muted-foreground italic text-xs">
                        No protocols found matching that query.
                    </div>
                </div>

                <div class="p-3 bg-muted/30 border-t border-border flex items-center justify-between px-6">
                    <div class="flex gap-4">
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                            <i data-lucide="corner-down-left" class="w-3 h-3"></i> Select
                        </span>
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                            <i data-lucide="arrow-up-down" class="w-3 h-3"></i> Navigate
                        </span>
                    </div>
                    <span class="text-[9px] font-black text-primary uppercase tracking-[0.2em]">ArchitGrid OS</span>
                </div>
            </div>
        </div>

        <div class="flex h-screen bg-background">
            <!-- Sidebar -->
            <aside class="w-64 bg-sidebar text-sidebar-foreground flex flex-col" x-data="{ openSwitcher: false }">
                <div class="p-6 border-b border-sidebar-border">
                    <a href="/dashboard" class="flex items-center gap-3">
                        <div class="w-10 h-10 shrink-0">
                            <img src="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png" class="w-full h-full object-contain" alt="ArchitGrid Logo">
                        </div>
                        <div>
                            <h1 class="text-sm font-black tracking-tight text-white uppercase">ArchitGrid</h1>
                            <p class="text-[10px] font-bold text-primary uppercase tracking-widest">Digital Architect</p>
                        </div>
                    </a>
                </div>

                <!-- Workspace Switcher Protocol -->
                <div class="px-4 py-4 border-b border-sidebar-border relative">
                    <button @click="openSwitcher = !openSwitcher" class="w-full flex items-center justify-between p-2 rounded-xl bg-sidebar-accent/50 border border-sidebar-border hover:border-primary/30 transition-all group">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <div class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                <i data-lucide="grid" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-white truncate">{{ app(\App\Models\Tenant::class)->name }}</span>
                        </div>
                        <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-500 group-hover:text-primary transition-colors"></i>
                    </button>

                    <!-- Switcher Dropdown -->
                    <div x-show="openSwitcher" @click.away="openSwitcher = false" x-cloak
                         class="absolute left-4 right-4 mt-2 bg-sidebar border border-sidebar-border rounded-xl shadow-2xl z-50 py-2 animate-in slide-in-from-top-2 duration-200">
                        <p class="px-4 py-2 text-[8px] font-black text-slate-500 uppercase tracking-widest">Authorized Nodes</p>
                        
                        @php
                            $user = auth()->user();
                            $baseTenant = \App\Models\Tenant::withoutGlobalScope('tenant')->find($user->tenant_id);
                            $availableTenants = collect([$baseTenant]);
                            if ($baseTenant->type === 'agency') {
                                $availableTenants = $availableTenants->concat($baseTenant->subAccounts);
                            }
                        @endphp

                        @foreach($availableTenants as $t)
                            <a href="{{ route('tenant.switch', $t->id) }}" 
                               class="flex items-center gap-3 px-4 py-2 hover:bg-sidebar-accent group transition-colors {{ app(\App\Models\Tenant::class)->id === $t->id ? 'bg-primary/5' : '' }}">
                                <div class="w-2 h-2 rounded-full {{ app(\App\Models\Tenant::class)->id === $t->id ? 'bg-primary animate-pulse' : 'bg-slate-700 group-hover:bg-slate-500' }}"></div>
                                <span class="text-[10px] font-bold {{ app(\App\Models\Tenant::class)->id === $t->id ? 'text-primary' : 'text-slate-400 group-hover:text-white' }} uppercase truncate">{{ $t->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <nav class="flex-1 p-4 overflow-y-auto custom-scrollbar">
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
                        <a href="{{ route('settings.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('settings') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            Settings
                        </a>
                    </div>
                </nav>

                <div class="p-4 border-t border-sidebar-border">
                    @if(session()->has('impersonated_by'))
                        <div class="mb-4 p-4 rounded-xl bg-red-600 text-white shadow-lg shadow-red-900/20 animate-pulse">
                            <p class="text-[9px] font-black uppercase tracking-widest mb-2">Impersonation Active</p>
                            <a href="{{ route('agency.impersonate.stop') }}" class="w-full h-10 bg-white text-red-600 rounded-lg flex items-center justify-center text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">
                                Exit Protocol
                            </a>
                        </div>
                    @endif

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
                        <h2 class="text-xl font-semibold text-foreground">Welcome Back to ArchitGrid</h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="relative group cursor-pointer" @click="showCommandPalette = true">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors"></i>
                            <div class="pl-9 w-80 bg-background flex h-10 w-full items-center rounded-md border border-input px-3 text-sm text-muted-foreground hover:border-primary/30 transition-all">
                                Architect your next move...
                                <span class="ml-auto mono text-[9px] font-black border border-border px-1.5 py-0.5 rounded uppercase opacity-50">Ctrl K</span>
                            </div>
                        </div>
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </button>
                        <!-- Intelligence Feed Dropdown -->
                        <div class="relative" x-data="{ 
                            openAlerts: false, 
                            unreadCount: {{ auth()->user()->unreadNotifications->count() }},
                            markAllAsRead() {
                                fetch('{{ route('notifications.read-all') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                .then(() => { this.unreadCount = 0; window.location.reload(); });
                            }
                        }">
                            <button @click="openAlerts = !openAlerts" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10 relative">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                <template x-if="unreadCount > 0">
                                    <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full ring-2 ring-background animate-pulse"></span>
                                </template>
                            </button>

                            <div x-show="openAlerts" @click.away="openAlerts = false" x-cloak
                                 class="absolute right-0 mt-4 w-96 bg-card border border-border rounded-3xl shadow-2xl z-[150] overflow-hidden animate-in slide-in-from-top-2 duration-200">
                                
                                <div class="p-6 border-b border-border bg-muted/30 flex items-center justify-between">
                                    <h3 class="text-sm font-black uppercase tracking-tighter">Intelligence Feed</h3>
                                    <template x-if="unreadCount > 0">
                                        <button @click="markAllAsRead" class="text-[9px] font-black uppercase text-primary hover:underline">Clear Registry</button>
                                    </template>
                                </div>

                                <div class="max-h-[400px] overflow-y-auto p-4 space-y-3 custom-scrollbar">
                                    @forelse(auth()->user()->notifications as $notification)
                                        <div class="p-4 rounded-2xl bg-muted/20 border border-border hover:border-primary/30 transition-all group relative">
                                            <div class="flex items-start gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0 border border-primary/20">
                                                    <i data-lucide="{{ $notification->data['icon'] ?? 'bell' }}" class="w-5 h-5"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-black uppercase tracking-tight text-foreground">{{ $notification->data['title'] }}</p>
                                                    <p class="text-[11px] text-muted-foreground italic leading-relaxed mt-1">{{ $notification->data['message'] }}</p>
                                                    <p class="text-[8px] font-mono text-slate-500 mt-2 uppercase tracking-widest">{{ \Carbon\Carbon::parse($notification->data['timestamp'])->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            @if(isset($notification->data['action_url']))
                                                <a href="{{ $notification->data['action_url'] }}" class="absolute inset-0 z-10"></a>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="py-12 text-center opacity-30 italic">
                                            <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-3"></i>
                                            <p class="text-xs font-bold uppercase tracking-widest">Feed is idle</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="p-4 border-t border-border bg-muted/30 text-center">
                                    <p class="mono text-[8px] font-black uppercase tracking-[0.4em] text-slate-500">Grid Alert Protocol v1.0</p>
                                </div>
                            </div>
                        </div>
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
        <script>
            if (window.lucide) {
                lucide.createIcons();
            }
        </script>
    </body>
</html>