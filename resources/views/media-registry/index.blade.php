@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto animate-in fade-in duration-700" x-data="{ 
    showPreviewModal: false, 
    selectedAsset: null,
    isUploading: false,
    triggerUpload() { this.$refs.fileInput.click() },
    handleUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.isUploading = true;
        const formData = new FormData();
        formData.append('file', file);
        
        fetch('{{ route('media-registry.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            } else {
                alert('Upload failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error uploading file.');
        })
        .finally(() => this.isUploading = false);
    },
    purgeAsset(id) {
        if(!confirm('Purge this visual from the grid registry?')) return;
        fetch(`/media-registry/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(() => window.location.reload());
    }
}">
    <!-- Hidden File Input -->
    <input type="file" x-ref="fileInput" class="hidden" accept="image/*" @change="handleUpload">

    <!-- Registry Header -->
    <div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground mb-2">Industrial Media Matrix</h1>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Protocol: Digital Asset Registry</span>
                </div>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-tighter italic">Total Index: {{ number_format($stats['total_assets']) }} nodes</span>
            </div>
        </div>
        
        <div class="flex gap-4">
            <button class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Filter Grid
            </button>
            
            <!-- Upload Button -->
            <button @click="triggerUpload()" :disabled="isUploading" class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all disabled:opacity-50">
                <i x-show="!isUploading" data-lucide="upload-cloud" class="w-4 h-4"></i>
                <i x-show="isUploading" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                <span x-text="isUploading ? 'Uploading...' : 'Upload Asset'"></span>
            </button>

            <a href="/content-creator" class="h-14 px-10 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                Provision New Asset
            </a>
        </div>
    </div>

    <!-- Media Telemetry -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                <i data-lucide="image" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Global Assets</p>
                <p class="text-2xl font-black text-white">{{ number_format($stats['total_assets']) }}</p>
            </div>
        </div>
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
            <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500">
                <i data-lucide="wand-2" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">AI Generated</p>
                <p class="text-2xl font-black text-white">{{ number_format($stats['ai_generated']) }}</p>
            </div>
        </div>
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                <i data-lucide="upload" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Local Uploads</p>
                <p class="text-2xl font-black text-white">{{ number_format($stats['uploads']) }}</p>
            </div>
        </div>
    </div>

    <!-- Assets Grid Matrix -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
        @forelse($assets as $asset)
            <div class="group relative aspect-square bg-card border border-border rounded-[24px] overflow-hidden hover:border-primary/50 transition-all shadow-sm">
                <!-- Visual Node -->
                <img src="{{ $asset->url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Asset">
                
                <!-- Metadata Overlay -->
                <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all flex flex-col justify-between p-6">
                    <div class="flex justify-between items-start">
                        <span class="px-2 py-0.5 rounded-lg bg-primary/20 text-primary text-[8px] font-black uppercase tracking-widest border border-primary/30">
                            {{ $asset->source === 'ai_generation' ? 'AI_PROV' : 'LOCAL_LINK' }}
                        </span>
                        <button @click="purgeAsset('{{ $asset->id }}')" class="w-8 h-8 rounded-lg bg-red-500/20 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <p class="text-[10px] font-black text-white uppercase truncate tracking-tight">{{ $asset->name }}</p>
                        <div class="flex gap-2">
                            <button @click="selectedAsset = @js($asset); showPreviewModal = true" class="flex-1 h-9 bg-white text-black rounded-xl font-black uppercase text-[8px] tracking-widest hover:bg-primary hover:text-white transition-all">Context</button>
                            <a href="{{ route('content-creator.index', ['media_id' => $asset->id]) }}" class="w-9 h-9 bg-primary text-black rounded-xl flex items-center justify-center hover:bg-white transition-all shadow-lg">
                                <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-40 text-center opacity-30 italic">
                <i data-lucide="image" class="w-16 h-16 mx-auto mb-6"></i>
                <p class="text-sm font-bold uppercase tracking-[0.2em]">Visual registry node empty</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $assets->links() }}
    </div>

    <!-- Identity Context Preview Modal -->
    <div x-show="showPreviewModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4 lg:p-20">
        <div @click.away="showPreviewModal = false" class="bg-card w-full max-w-6xl h-full max-h-[85vh] rounded-[40px] shadow-2xl border border-border overflow-hidden flex flex-col lg:flex-row animate-in zoom-in-95 duration-300">
            
            <!-- Asset Section -->
            <div class="flex-1 bg-black flex items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 grid-canvas opacity-20 pointer-events-none"></div>
                <img :src="selectedAsset?.url" class="max-w-full max-h-full object-contain relative z-10">
            </div>

            <!-- Identity Section -->
            <div class="w-full lg:w-[400px] border-l border-border flex flex-col shrink-0">
                <div class="p-8 border-b border-border bg-muted/30">
                    <div class="flex justify-between items-start mb-6">
                        <span class="mono text-[8px] font-black uppercase tracking-[0.4em] text-primary">Identity Context</span>
                        <button @click="showPreviewModal = false" class="w-8 h-8 rounded-full hover:bg-muted transition-colors flex items-center justify-center"><i data-lucide="x" class="w-4 h-4 text-slate-500"></i></button>
                    </div>
                    <h2 class="text-xl font-black uppercase tracking-tight text-white mb-2" x-text="selectedAsset?.name"></h2>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest" x-text="'Stored: ' + selectedAsset?.created_at"></p>
                </div>

                <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                    <!-- Source Node -->
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-widest text-primary italic">Provisioning Source</label>
                        <div class="p-4 rounded-2xl bg-muted/20 border border-border flex items-center gap-4">
                            <i :data-lucide="selectedAsset?.source === 'ai_generation' ? 'sparkles' : 'upload'" class="w-5 h-5 text-slate-400"></i>
                            <span class="text-xs font-bold text-foreground uppercase tracking-tight" x-text="selectedAsset?.source"></span>
                        </div>
                    </div>

                    <!-- AI Protocol Prompt -->
                    <template x-if="selectedAsset?.prompt">
                        <div class="space-y-3">
                            <label class="text-[9px] font-black uppercase tracking-widest text-primary italic">Original Generation Prompt</label>
                            <div class="p-6 rounded-3xl bg-slate-950/50 border border-white/5 mono text-[10px] text-slate-400 italic leading-relaxed" x-text="selectedAsset?.prompt"></div>
                        </div>
                    </template>

                    <!-- Technical Specs -->
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-widest text-primary italic">Identity Attributes</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-muted/10 border border-border">
                                <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Type</p>
                                <p class="text-[10px] font-bold text-foreground uppercase tracking-widest" x-text="selectedAsset?.type"></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-muted/10 border border-border">
                                <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Resolution</p>
                                <p class="text-[10px] font-bold text-foreground uppercase tracking-widest">1024 x 1024</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-border bg-muted/30">
                    <button class="w-full h-14 bg-primary text-primary-foreground rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                        Synchronize to Content Architect
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
