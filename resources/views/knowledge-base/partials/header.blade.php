{{-- Knowledge Hub Page Header --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Global Knowledge Hub</h1>
        <p class="text-muted-foreground font-medium italic">Your agency's central intelligence repository for RAG-driven AI grounding.</p>
    </div>
    <div class="flex gap-3">
        <button @click="showFolderModal = true" class="bg-card border border-border text-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-muted transition-all">
            <i data-lucide="folder-plus" class="w-4 h-4 mr-2 inline"></i>
            New Folder
        </button>
        <button @click="showAddModal = true" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Index New Asset
        </button>
    </div>
</div>

{{-- Breadcrumbs --}}
<div x-show="currentFolder" class="mb-6 flex items-center gap-2 text-sm text-muted-foreground animate-in fade-in slide-in-from-top-2">
    <a href="{{ route('knowledge-base.index') }}" class="hover:text-primary transition-colors flex items-center gap-1">
        <i data-lucide="home" class="w-3 h-3"></i> Root
    </a>
    <span>/</span>
    <span class="font-bold text-foreground" x-text="currentFolder?.title"></span>
    <a x-show="currentFolder" :href="'/knowledge-base?folder=' + (currentFolder ? currentFolder.parent_id : '')" class="ml-4 text-xs bg-muted px-2 py-1 rounded hover:bg-muted/80 transition-colors">
        <i data-lucide="arrow-up" class="w-3 h-3 inline mr-1"></i> Up
    </a>
</div>
