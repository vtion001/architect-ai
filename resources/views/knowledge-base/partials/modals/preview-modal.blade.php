{{-- Knowledge Hub Asset Preview Modal --}}
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
