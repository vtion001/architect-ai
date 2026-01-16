{{-- Research Engine - Success Modal --}}
<div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div @click.away="showSuccessModal = false" class="bg-card w-full max-w-sm rounded-[40px] shadow-2xl border border-border p-10 text-center animate-in zoom-in-95 duration-300 relative overflow-hidden">
        <div class="absolute inset-0 bg-primary/5 pointer-events-none"></div>
        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6 shadow-sm">
            <i data-lucide="check" class="w-10 h-10 text-green-600"></i>
        </div>
        <h2 class="text-2xl font-black text-foreground mb-2 uppercase tracking-tighter">Protocol Success</h2>
        <p class="text-muted-foreground mb-10 leading-relaxed text-sm font-medium italic">
            The research protocol has been finalized and grounded.
        </p>
        <div class="flex flex-col gap-3">
            <a :href="'/research-engine/' + createdResearchId" 
               class="w-full h-14 rounded-2xl bg-primary text-primary-foreground font-black uppercase tracking-[0.2em] text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                <i data-lucide="eye" class="w-4 h-4"></i>
                Preview Intelligence
            </a>
            <button @click="showSuccessModal = false" class="w-full h-14 rounded-2xl bg-muted text-muted-foreground font-black uppercase tracking-[0.2em] text-xs hover:bg-muted/80 transition-all">Dismiss</button>
        </div>
    </div>
</div>
