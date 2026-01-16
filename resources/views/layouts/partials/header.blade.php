{{-- Main Header Bar --}}
<header class="bg-card border-b border-border px-8 py-4 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-semibold text-foreground">Welcome Back to ArchitGrid</h2>
    </div>

    <div class="flex items-center gap-4">
        {{-- Search Trigger --}}
        <div class="relative group cursor-pointer" @click="showCommandPalette = true">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors"></i>
            <div class="pl-9 w-80 bg-background flex h-10 w-full items-center rounded-md border border-input px-3 text-sm text-muted-foreground hover:border-primary/30 transition-all">
                Architect your next move...
                <span class="ml-auto mono text-[9px] font-black border border-border px-1.5 py-0.5 rounded uppercase opacity-50">Ctrl K</span>
            </div>
        </div>
        
        {{-- Messages Button --}}
        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
            <i data-lucide="message-square" class="w-5 h-5"></i>
        </button>
        
        {{-- Notifications Dropdown --}}
        @include('layouts.partials.header.notifications')
        
        {{-- User Avatar --}}
        <div class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-full">
            <div class="flex h-full w-full items-center justify-center rounded-full bg-muted">AA</div>
        </div>
    </div>
</header>
