{{-- Media Registry - Asset Card --}}
<div class="group relative aspect-square bg-card border border-border rounded-[24px] overflow-hidden hover:border-primary/50 transition-all shadow-sm">
    <!-- Visual Node -->
    @if($asset->type === 'audio')
        <div class="w-full h-full flex flex-col items-center justify-center bg-slate-900 group-hover:bg-slate-800 transition-colors">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                <i data-lucide="file-audio" class="w-8 h-8 text-primary"></i>
            </div>
            <div class="flex items-center gap-1 opacity-50">
                <div class="w-1 h-4 bg-primary rounded-full animate-pulse" style="animation-delay: 0.1s"></div>
                <div class="w-1 h-6 bg-primary rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                <div class="w-1 h-3 bg-primary rounded-full animate-pulse" style="animation-delay: 0.3s"></div>
                <div class="w-1 h-5 bg-primary rounded-full animate-pulse" style="animation-delay: 0.1s"></div>
                <div class="w-1 h-4 bg-primary rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
            </div>
            <audio src="{{ $asset->url }}" class="absolute bottom-4 left-4 right-4 z-20 w-[calc(100%-32px)] opacity-0 group-hover:opacity-100 transition-opacity" controls controlsList="nodownload noplaybackrate"></audio>
        </div>
    @else
        <img src="{{ $asset->url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Asset">
    @endif
    
    <!-- Metadata Overlay -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all flex flex-col justify-between p-6 pointer-events-none">
        <div class="flex justify-between items-start pointer-events-auto">
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
