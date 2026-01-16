{{-- Policies Index - Security Health Bar --}}
<div class="mb-12 p-6 rounded-[32px] bg-primary/5 border border-primary/10 flex items-center justify-between relative overflow-hidden group">
    <div class="flex items-center gap-6 relative z-10">
        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
            <i data-lucide="shield-check" class="w-8 h-8"></i>
        </div>
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest">Active Grid Protection</h3>
            <p class="text-xs text-muted-foreground font-medium italic">Current policy stack: <span class="text-foreground font-bold">{{ $policies->count() }} active nodes</span>. All isolation boundaries verified.</p>
        </div>
    </div>
    <div class="flex items-center gap-2 relative z-10">
        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
        <span class="text-[9px] font-black uppercase text-green-500 tracking-widest">Isolation Secured</span>
    </div>
    <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
        <i data-lucide="shield" class="w-32 h-32 text-primary"></i>
    </div>
</div>
