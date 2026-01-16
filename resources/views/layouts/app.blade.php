{{--
    Main App Layout
    
    Core application layout with sidebar, header, and content area.
    Modularized - uses @include for partials.
    
    Structure:
    - Head (meta, styles, scripts)
    - Command Palette
    - Sidebar (logo, switcher, navigation, footer)
    - Main Content (header, content area)
    - Global Scripts & Widgets
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.partials.head')
    
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
        
        {{-- Command Palette Overlay --}}
        @include('layouts.partials.command-palette')

        <div class="flex h-screen bg-background">
            {{-- Sidebar --}}
            @include('layouts.partials.sidebar')

            {{-- Main Content --}}
            <main class="flex-1 flex flex-col overflow-hidden">
                {{-- Header Bar --}}
                @include('layouts.partials.header')

                {{-- Content Area --}}
                <div class="flex-1 overflow-auto">
                    @yield('content')
                </div>
            </main>
        </div>
        
        {{-- Global Scripts & Widgets --}}
        @include('layouts.partials.scripts')
    </body>
</html>