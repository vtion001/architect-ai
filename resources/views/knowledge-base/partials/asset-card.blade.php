{{-- Knowledge Hub Asset Card --}}
@props(['asset'])

<div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
    {{-- Type Badge --}}
    <div class="flex items-center justify-between mb-8">
        <span class="px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-[9px] font-black uppercase tracking-widest border border-primary/20">
            {{ strtoupper($asset->type) }}
        </span>
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
            <span class="mono text-[8px] text-slate-500 uppercase tracking-widest">RAG Optimized</span>
        </div>
    </div>

    {{-- Asset Identity --}}
    <div class="space-y-4 mb-8 flex-1">
        <h3 class="text-xl font-black text-foreground truncate uppercase tracking-tight group-hover:text-primary transition-colors">{{ $asset->title }}</h3>
        <p class="text-xs text-muted-foreground font-medium italic line-clamp-3 leading-relaxed">
            {{ Str::limit($asset->content, 150) }}
        </p>
    </div>

    {{-- Metadata --}}
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

    {{-- Actions --}}
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
