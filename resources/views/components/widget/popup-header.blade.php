{{--
    Note/Task Widget - Popup Header
    
    ISOLATED: This partial controls ONLY the header section.
    Changes to tab content or logic will NOT affect the header layout.
--}}

<div class="px-4 py-3 border-b border-border shrink-0" 
     style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(99, 102, 241, 0.02));">
    
    {{-- Title Row --}}
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-xs font-black uppercase tracking-widest text-foreground/80 flex items-center gap-2">
            <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-indigo-500"></i>
            Command Center
        </h3>
        <div class="flex items-center gap-1">
            <button @click="showSearch = !showSearch" 
                    :class="showSearch ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground'"
                    class="p-1.5 rounded-md transition-colors" 
                    title="Search">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
            <button @click="isOpen = false" 
                    class="p-1.5 hover:bg-muted rounded-md transition-colors text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    {{-- Segmented Tab Control - ISOLATED from tab content --}}
    @include('components.widget.tab-navigation')
</div>
