{{--
    Note/Task Widget - Container Layout
    
    ISOLATED: This partial controls ONLY the widget popup container layout.
    Changes to widget functionality will NOT affect the container position.
    
    Fixed Position:
    - bottom: 24px
    - right: 94px (aligned with AI chat)
    - width: 380px
    - height: 580px
    - z-index: 99998
    
    The container position is INDEPENDENT of tab logic and data fetching.
--}}

<div x-show="isOpen" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95 translate-x-4"
     x-transition:enter-end="opacity-100 scale-100 translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100 translate-x-0"
     x-transition:leave-end="opacity-0 scale-95 translate-x-4"
     class="widget-popup-container">
     
    {{-- Header - ISOLATED --}}
    @include('components.widget.popup-header')

    {{-- Search Bar --}}
    <div x-show="showSearch" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="px-4 py-2 border-b border-border bg-card">
        <input type="text" 
               x-model="searchQuery" 
               placeholder="Search across all modules..." 
               class="w-full bg-muted/30 border border-border rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all">
    </div>

    {{-- Content Area - ISOLATED (Tab content loaded via partials) --}}
    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-background">
        <div x-show="activeTab === 'tasks'">
            @include('components.widget.tasks-tab')
        </div>
        <div x-show="activeTab === 'notes'">
            @include('components.widget.notes-tab')
        </div>
        <div x-show="activeTab === 'voice'" class="h-full">
            @include('components.widget.voice-tab')
        </div>
        <div x-show="activeTab === 'studio'">
            @include('components.widget.studio-tab')
        </div>
        <div x-show="activeTab === 'history'">
            @include('components.widget.history-tab')
        </div>
    </div>
    
    {{-- Global Overlays (Modals) --}}
    @include('components.widget.widget-modals')
</div>

<style>
/* Popup Container - FIXED POSITION (Never changes) */
.widget-popup-container {
    position: fixed;
    bottom: 24px;
    right: 94px;
    z-index: 99998;
    
    /* Size */
    width: 380px;
    max-width: calc(100vw - 48px);
    height: 580px;
    max-height: calc(100vh - 48px);
    
    /* Appearance - Solid overlay with glassmorphism */
    background-color: hsl(var(--card));
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid hsl(var(--border));
    border-radius: 1rem;
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.35),
        0 0 0 1px rgba(0, 0, 0, 0.05),
        0 10px 25px -5px rgba(0, 0, 0, 0.1);
    
    /* Layout */
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
</style>
