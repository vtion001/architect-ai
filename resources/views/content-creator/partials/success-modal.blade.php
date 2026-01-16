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
         class="bg-card w-full max-w-sm rounded-2xl shadow-2xl border border-border p-8 text-center animate-in zoom-in-95 duration-300 relative overflow-hidden">
        
        {{-- Confetti/Sparkles Background Effect --}}
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/10 via-transparent to-primary/5 pointer-events-none"></div>

        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6 shadow-sm relative z-10">
            <i data-lucide="check" class="w-8 h-8 text-green-600"></i>
        </div>
        
        <h2 class="text-2xl font-black text-foreground mb-2 relative z-10">Success!</h2>
        <p class="text-muted-foreground mb-8 leading-relaxed text-sm font-medium relative z-10">
            Your content has been successfully architected and is ready for review.
        </p>
        
        <div class="flex flex-col gap-3 relative z-10">
            <a :href="'/content-creator/' + createdContentId" 
               class="w-full h-12 rounded-xl bg-primary text-primary-foreground font-black uppercase tracking-widest text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                <i data-lucide="eye" class="w-4 h-4"></i>
                View Content
            </a>
            <button @click="showSuccessModal = false" 
                    class="w-full h-12 rounded-xl bg-muted text-muted-foreground font-black uppercase tracking-widest text-xs hover:bg-muted/80 transition-all">
                Create More
            </button>
        </div>
    </div>
</div>
