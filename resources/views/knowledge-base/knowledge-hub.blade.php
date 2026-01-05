@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showAddModal: false,
    showViewModal: false,
    selectedAsset: null,
    
    // New Asset Form
    newAsset: {
        title: '',
        type: 'text',
        category: 'Market Intelligence',
        content: '',
        source_url: ''
    },
    isSaving: false,

    saveAsset() {
        if (!this.newAsset.title || !this.newAsset.content) {
            alert('Title and Content are mandatory.');
            return;
        }
        this.isSaving = true;
        fetch('{{ route('knowledge-base.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(this.newAsset)
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
    }
}">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Global Knowledge Hub</h1>
            <p class="text-muted-foreground font-medium italic">Your agency's central intelligence repository for RAG-driven AI grounding.</p>
        </div>
        <button @click="showAddModal = true" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Index New Asset
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
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
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <i data-lucide="database" class="w-16 h-16 mx-auto text-slate-300"></i>
                <p class="text-sm font-medium">Your global knowledge base is currently empty.</p>
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
                            <option value="file">Local Documentation</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Intelligence Category</label>
                    <input x-model="newAsset.category" type="text" placeholder="e.g., Client Alpha Context"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Source Content</label>
                    <textarea x-model="newAsset.content" rows="8" required placeholder="Paste the intelligence data here..."
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
