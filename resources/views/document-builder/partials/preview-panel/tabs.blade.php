{{-- Preview Panel Tabs and Zoom Controls --}}
{{-- Expects parent x-data with: activeTab, zoomLevel --}}
<div class="flex items-center justify-between px-1">
    <div class="flex items-center gap-4 bg-muted/30 p-1 rounded-2xl">
        <button @click="activeTab = 'preview'" 
                :class="activeTab === 'preview' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
            Preview
        </button>
        <button @click="activeTab = 'html'" 
                :class="activeTab === 'html' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
            HTML Code
        </button>
    </div>
    <div class="flex items-center gap-4">
        <button @click="zoomLevel = Math.max(0.3, zoomLevel - 0.05)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="minus-circle" class="w-4 h-4"></i></button>
        <span class="mono text-[10px] font-black text-slate-400" x-text="Math.round(zoomLevel * 100) + '%'"></span>
        <button @click="zoomLevel = Math.min(1.0, zoomLevel + 0.05)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="plus-circle" class="w-4 h-4"></i></button>
    </div>
</div>
