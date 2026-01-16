{{-- Research Grounding Input --}}
{{-- Expects parent x-data with: researchTopic --}}
<div class="space-y-3">
    <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic px-1">Research Grounding</label>
    <div class="relative">
        <i data-lucide="brain" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-primary"></i>
        <input x-model="researchTopic" type="text" placeholder="e.g. Q3 Market Sentiment"
               class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 pr-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
    </div>
    <p class="text-[9px] text-muted-foreground italic px-1">AI will perform a deep sweep based on this identity.</p>
</div>
