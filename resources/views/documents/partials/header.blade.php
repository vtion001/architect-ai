{{-- Document Viewer - Header --}}
<div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
    <div>
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('documents.index') }}" class="w-10 h-10 rounded-xl bg-muted/50 border border-border flex items-center justify-center hover:bg-primary/10 hover:border-primary/30 transition-all group">
                <i data-lucide="arrow-left" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
            </a>
            <div class="flex flex-col">
                <span class="mono text-[10px] font-black uppercase tracking-[0.3em] text-primary">Protocol: Archived Intelligence</span>
                <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground">{{ $document->name }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-6 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">{{ $document->category ?? 'General' }} Archive</span>
            </div>
            <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Asset UUID: {{ substr($document->id, 0, 13) }}...</span>
            <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Stored: {{ $document->created_at->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <div class="flex items-center gap-3">
        {{-- Zoom Controls --}}
        <div class="flex items-center gap-4 mr-4 bg-muted/30 p-1 rounded-xl px-3 h-10">
            <button @click="zoomLevel = Math.max(0.3, zoomLevel - 0.1)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="minus-circle" class="w-4 h-4"></i></button>
            <span class="mono text-[10px] font-black text-slate-400 w-8 text-center" x-text="Math.round(zoomLevel * 100) + '%'"></span>
            <button @click="zoomLevel = Math.min(1.2, zoomLevel + 0.1)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="plus-circle" class="w-4 h-4"></i></button>
        </div>

        {{-- Edit Controls --}}
        <button @click="isEditing = true" x-show="!isEditing" class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
            Edit Document
        </button>

        <div x-show="isEditing" class="flex gap-2" style="display: none;">
            <button @click="isEditing = false; window.location.reload()" class="h-14 px-6 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest hover:bg-red-50 hover:text-red-600 transition-all">
                Cancel
            </button>
            <button @click="saveDocument" :disabled="isSaving" class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all disabled:opacity-50">
                <i x-show="!isSaving" data-lucide="save" class="w-4 h-4"></i>
                <i x-show="isSaving" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>

        {{-- Export Actions --}}
        <button x-show="!isEditing" @click="downloadPdf" class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export Archive
        </button>
    </div>
</div>
