{{-- Sidebar Navigation Menu --}}
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

        <a href="/ai-agents" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('ai-agents*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="bot" class="w-4 h-4"></i>
            AI Agents
        </a>

        <a href="/document-builder" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('document-builder') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            Document Builder
        </a>

        <a href="/documents" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('documents') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            Documents
        </a>

        <a href="/media-registry" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('media-registry') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="image" class="w-4 h-4"></i>
            Media Registry
        </a>

        <a href="{{ route('brands.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('settings/brands*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="fingerprint" class="w-4 h-4"></i>
            Brand Kits
        </a>

        <a href="/analytics" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('analytics') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
            Analytics
        </a>
    </div>

    <div class="mt-8 space-y-1">
        <p class="text-xs font-semibold text-sidebar-foreground/50 px-3 mb-3">HELP & SUPPORT</p>
        <a href="{{ route('help-center.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('help-center*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="help-circle" class="w-4 h-4"></i>
            Help & Center
        </a>
        <a href="{{ route('settings.index') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->is('settings') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground' }}">
            <i data-lucide="settings" class="w-4 h-4"></i>
            Settings
        </a>
    </div>
</nav>
