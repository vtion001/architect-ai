@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showAddModal: false,
    showFolderModal: false,
    showViewModal: false,
    selectedAsset: null,
    currentFolder: @js($currentFolder ?? null),
    
    // New Asset Form
    newAsset: {
        title: '',
        type: 'text',
        category: 'Market Intelligence',
        content: '',
        source_url: ''
    },
    newFolderTitle: '',
    isSaving: false,

    createFolder() {
        if (!this.newFolderTitle) return;
        this.isSaving = true;
        const formData = new FormData();
        formData.append('title', this.newFolderTitle);
        formData.append('type', 'folder');
        if (this.currentFolder) formData.append('parent_id', this.currentFolder.id);

        fetch('{{ route('knowledge-base.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) window.location.reload();
            else this.isSaving = false;
        });
    },

    saveAsset() {
        if (!this.newAsset.title) {
            alert('Title is mandatory.');
            return;
        }
        this.isSaving = true;
        const formData = new FormData();
        formData.append('title', this.newAsset.title);
        formData.append('type', this.newAsset.type);
        formData.append('category', this.newAsset.category);
        formData.append('content', this.newAsset.content || '');
        if (this.newAsset.source_url) formData.append('source_url', this.newAsset.source_url);
        if (this.currentFolder) formData.append('parent_id', this.currentFolder.id);
        
        if (this.$refs.fileInput && this.$refs.fileInput.files.length) {
            formData.append('file', this.$refs.fileInput.files[0]);
        }

        fetch('{{ route('knowledge-base.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Save failed.');
                this.isSaving = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSaving = false;
        });
    },

    deleteAsset(id) {
        if (!confirm('Are you sure you want to delete this asset? This cannot be undone.')) return;

        fetch(`/knowledge-base/${id}`, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.reload();
            else alert('Delete failed.');
        });
    }
}">
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

    <!-- Breadcrumbs -->
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

    <!-- Stats Grid -->
    <div x-show="!currentFolder" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Assets</p>
                <p class="text-2xl font-black text-white">{{ $stats['total_docs'] }}</p>
            </div>
        </div>
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Categories</p>
                <p class="text-2xl font-black text-white">{{ $stats['categories'] }}</p>
            </div>
        </div>
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500">
                <i data-lucide="refresh-ccw" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Active Syncs</p>
                <p class="text-2xl font-black text-white">{{ $stats['recent_updates'] }}</p>
            </div>
        </div>
    </div>

    <!-- Assets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($assets as $asset)
            @if($asset->type === 'folder')
                <!-- Folder Card -->
                <a href="{{ route('knowledge-base.index', ['folder' => $asset->id]) }}" class="bg-card border border-border rounded-[32px] p-6 shadow-sm hover:border-primary/50 transition-all group flex items-center gap-4 relative overflow-hidden">
                    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shrink-0">
                        <i data-lucide="folder" class="w-7 h-7 fill-current"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-foreground uppercase tracking-tight group-hover:text-primary transition-colors">{{ $asset->title }}</h3>
                        <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Directory</p>
                    </div>
                    <div class="ml-auto flex items-center gap-2 relative z-10">
                        <button @click.prevent="deleteAsset('{{ $asset->id }}')" class="opacity-0 group-hover:opacity-100 p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-all">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors"></i>
                    </div>
                </a>
            @else
                <!-- File/Asset Card -->
                <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
                    <!-- Type Badge -->
                    <div class="flex items-center justify-between mb-8">
                        <span class="px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-[9px] font-black uppercase tracking-widest border border-primary/20">
                            {{ strtoupper($asset->type) }}
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="mono text-[8px] text-slate-500 uppercase tracking-widest">RAG Optimized</span>
                        </div>
                    </div>

                    <!-- Asset Identity -->
                    <div class="space-y-4 mb-8 flex-1">
                        <h3 class="text-xl font-black text-foreground truncate uppercase tracking-tight group-hover:text-primary transition-colors">{{ $asset->title }}</h3>
                        <p class="text-xs text-muted-foreground font-medium italic line-clamp-3 leading-relaxed">
                            {{ Str::limit($asset->content, 150) }}
                        </p>
                    </div>

                    <!-- Metadata -->
                    <div class="flex items-center gap-6 mb-8 pt-6 border-t border-border/50">
                        <div class="flex items-center gap-2">
                            <i data-lucide="tag" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $asset->category }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $asset->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button @click="selectedAsset = @js($asset); showViewModal = true" 
                                class="flex-1 py-3 rounded-xl bg-muted/50 border border-border font-black uppercase text-[9px] tracking-widest hover:bg-white hover:text-black transition-all">
                            Preview Intelligence
                        </button>
                        <a href="{{ route('content-creator.index', ['context_asset' => $asset->id]) }}" 
                           title="Architect Content using this context"
                           class="w-12 h-12 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center hover:bg-primary hover:text-black transition-all">
                            <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                        </a>
                        <button @click="deleteAsset('{{ $asset->id }}')" 
                                class="w-12 h-12 rounded-xl bg-red-500/10 text-red-500 border border-red-500/20 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <i data-lucide="folder-open" class="w-16 h-16 mx-auto text-slate-300"></i>
                <p class="text-sm font-medium">This directory is currently empty.</p>
            </div>
        @endforelse
    </div>

    <!-- Asset Preview Modal -->
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4 lg:p-10">
        <div @click.away="showViewModal = false" 
             class="bg-card w-full max-w-4xl h-full max-h-[85vh] rounded-[40px] shadow-2xl border border-border overflow-hidden flex flex-col animate-in zoom-in-95 duration-300">
            
            <div class="p-8 border-b border-border bg-muted/30 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                        <i data-lucide="book-open" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tighter text-foreground" x-text="selectedAsset?.title"></h2>
                        <div class="flex gap-4 mt-1">
                            <span class="text-[9px] font-black uppercase text-primary tracking-widest" x-text="'Type: ' + selectedAsset?.type"></span>
                            <span class="text-[9px] font-black uppercase text-muted-foreground tracking-widest" x-text="'Category: ' + selectedAsset?.category"></span>
                        </div>
                    </div>
                </div>
                <button @click="showViewModal = false" class="w-10 h-10 rounded-full hover:bg-muted transition-colors flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-12 bg-white/5 custom-scrollbar">
                <div class="prose prose-sm max-w-none text-foreground font-medium leading-relaxed whitespace-pre-wrap" x-text="selectedAsset?.content"></div>
                
                <template x-if="selectedAsset?.source_url">
                    <div class="mt-8 pt-8 border-t border-border">
                        <a :href="selectedAsset?.source_url" target="_blank" class="text-xs font-bold text-primary hover:underline flex items-center gap-2">
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                            View Source Asset
                        </a>
                    </div>
                </template>
            </div>

            <div class="p-8 border-t border-border bg-muted/30 flex justify-between items-center shrink-0">
                <p class="mono text-[8px] font-black uppercase tracking-[0.4em] text-muted-foreground">ArchitGrid Intelligence Node v1.0.4</p>
                <div class="flex gap-3">
                    <button @click="showViewModal = false" class="h-12 px-8 rounded-xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-muted transition-all">Dismiss</button>
                    <a :href="'/content-creator?topic=' + encodeURIComponent(selectedAsset?.title)" 
                       class="h-12 px-8 rounded-xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 hover:scale-[1.02] transition-all">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        Architect from Context
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div x-show="showFolderModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="!isSaving && (showFolderModal = false)" class="bg-card w-full max-w-md rounded-[32px] shadow-2xl border border-border p-8 animate-in zoom-in-95 duration-200">
            <h3 class="text-xl font-black uppercase tracking-tighter mb-6">Create Directory</h3>
            <input x-model="newFolderTitle" type="text" placeholder="Folder Name" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none mb-6">
            <div class="flex justify-end gap-3">
                <button @click="showFolderModal = false" class="px-6 py-3 rounded-xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-muted transition-all">Cancel</button>
                <button @click="createFolder" :disabled="isSaving" class="px-6 py-3 rounded-xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-widest hover:bg-primary/90 transition-all">Create</button>
            </div>
        </div>
    </div>

    <!-- Index New Asset Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="!isSaving && (showAddModal = false)" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 relative overflow-hidden">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Index New Intelligence</h2>
            <p class="text-sm text-muted-foreground mb-10 italic">Store corporate data or research to ground AI generations.</p>
            
            <form @submit.prevent="saveAsset" class="space-y-6 relative z-10">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Asset Title</label>
                        <input x-model="newAsset.title" type="text" required placeholder="e.g., Q1 Strategy Brief"
                               class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Asset Type</label>
                        <select x-model="newAsset.type" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="text">Raw Intelligence</option>
                            <option value="website">External Domain</option>
                            <option value="file">File Upload (PDF/MD/TXT)</option>
                        </select>
                    </div>
                </div>

                <!-- File Upload Input (Conditional) -->
                <div class="space-y-2" x-show="newAsset.type === 'file'">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Upload Document</label>
                    <div class="border-2 border-dashed border-border rounded-2xl p-6 flex flex-col items-center justify-center hover:border-primary/50 transition-colors">
                        <i data-lucide="upload-cloud" class="w-8 h-8 text-primary mb-2"></i>
                        <input type="file" x-ref="fileInput" class="w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Intelligence Category</label>
                    <input x-model="newAsset.category" type="text" placeholder="e.g., Client Alpha Context"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div class="space-y-2" x-show="newAsset.type !== 'file'">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Source Content</label>
                    <textarea x-model="newAsset.content" rows="8" :required="newAsset.type !== 'file'" placeholder="Paste the intelligence data here..."
                              class="w-full bg-muted/20 border border-border rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                </div>

                <div class="pt-6 flex flex-col gap-3">
                    <button type="submit" :disabled="isSaving" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                        <template x-if="isSaving">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isSaving ? 'INDEXING...' : 'INITIALIZE INDEXING'"></span>
                    </button>
                    <button type="button" @click="showAddModal = false" :disabled="isSaving" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Abort</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
