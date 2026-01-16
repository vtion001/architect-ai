{{-- Media Registry - Page Header --}}
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
