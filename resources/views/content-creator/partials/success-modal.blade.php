{{-- Success Modal Partial --}}
<div x-show="showSuccessModal" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="showSuccessModal = false" 
         class="bg-card w-full max-w-2xl rounded-2xl shadow-2xl border border-border p-8 text-center animate-in zoom-in-95 duration-300 relative overflow-hidden max-h-[90vh] overflow-y-auto">
        
        {{-- Confetti/Sparkles Background Effect --}}
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/10 via-transparent to-primary/5 pointer-events-none"></div>

        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6 shadow-sm relative z-10">
            <i data-lucide="check" class="w-8 h-8 text-green-600"></i>
        </div>
        
        <h2 class="text-2xl font-black text-foreground mb-2 relative z-10">Success!</h2>
        <p class="text-muted-foreground mb-6 leading-relaxed text-sm font-medium relative z-10">
            <span x-show="generator === 'blog' && isBatchMode && batchChildren.length > 0">
                Your blog batch is ready — <span x-text="batchChildren.length"></span> posts generated.
            </span>
            <span x-show="!(generator === 'blog' && isBatchMode && batchChildren.length > 0)">
                Your content has been successfully architected and is ready for review.
            </span>
        </p>

        {{-- Batch Results (visible for blog batch mode) --}}
        <div x-show="generator === 'blog' && isBatchMode && batchChildren.length > 0" 
             class="relative z-10 text-left mb-6 space-y-3 max-h-80 overflow-y-auto">
            <template x-for="(child, idx) in batchChildren" :key="child.id">
                <div class="bg-white/50 border border-border rounded-xl p-4 hover:border-primary/30 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 text-xs font-black text-primary" x-text="idx + 1"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <p class="text-sm font-bold text-foreground truncate" x-text="child.title"></p>
                                <span class="text-[10px] font-black uppercase tracking-wider text-muted-foreground shrink-0" x-text="child.word_count + ' words'"></span>
                            </div>
                            <p class="text-[10px] text-muted-foreground italic mb-2" x-text="child.angle"></p>
                            <p class="text-[10px] text-muted-foreground line-clamp-2 mb-3" x-text="child.excerpt"></p>
                            <div class="flex items-center gap-2">
                                <a :href="'/content-creator/' + child.id" 
                                   class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-[10px] font-black uppercase tracking-wider hover:bg-primary/20 transition-colors">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <div class="flex flex-col gap-3 relative z-10">
            <a :href="'/content-creator/' + createdContentId" 
               class="w-full h-12 rounded-xl bg-primary text-primary-foreground font-black uppercase tracking-widest text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                <i data-lucide="eye" class="w-4 h-4"></i>
                <span x-text="generator === 'blog' && isBatchMode ? 'View Full Batch' : 'View Content'"></span>
            </a>
            <button @click="showSuccessModal = false; batchChildren = []" 
                    class="w-full h-12 rounded-xl bg-muted text-muted-foreground font-black uppercase tracking-widest text-xs hover:bg-muted/80 transition-all">
                Create More
            </button>
        </div>
    </div>
</div>
