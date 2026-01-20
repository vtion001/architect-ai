{{--
    Note/Task Widget - Tab Navigation
    
    ISOLATED: This partial controls ONLY the tab navigation buttons.
    The actual tab content is loaded from separate partials.
    
    Tabs:
    - tasks (Check Square icon)
    - notes (Sticky Note icon)
    - voice (Mic icon) 
    - history (History icon)
--}}

<div class="flex bg-muted/50 rounded-xl p-1 border border-border/50">
    {{-- Tasks Tab --}}
    <button @click="activeTab = 'tasks'" 
            :class="activeTab === 'tasks' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
            class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" 
            title="Tasks">
        <i data-lucide="check-square" class="w-4 h-4"></i>
    </button>
    
    {{-- Notes Tab --}}
    <button @click="activeTab = 'notes'" 
            :class="activeTab === 'notes' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
            class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" 
            title="Notes">
        <i data-lucide="sticky-note" class="w-4 h-4"></i>
    </button>
    
    {{-- Voice/Meeting Scribe Tab --}}
    <button @click="activeTab = 'voice'" 
            :class="activeTab === 'voice' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
            class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" 
            title="Meeting Scribe">
        <i data-lucide="mic" class="w-4 h-4"></i>
    </button>
    
    
    
    {{-- History/Archive Tab --}}
    <button @click="activeTab = 'history'" 
            :class="activeTab === 'history' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
            class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" 
            title="Archive">
        <i data-lucide="history" class="w-4 h-4"></i>
    </button>
</div>
