{{-- Analytics - Core Metrics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Registry Success</p>
        <p class="text-4xl font-black text-foreground">{{ $successRate }}%</p>
        <div class="mt-6 w-full bg-muted h-1 rounded-full overflow-hidden">
            <div class="bg-green-500 h-full" style="width: {{ $successRate }}%"></div>
        </div>
        <i data-lucide="shield-check" class="absolute -right-4 -bottom-4 w-20 h-20 text-green-500/5 group-hover:scale-110 transition-transform"></i>
    </div>

    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Treasury Output</p>
        <p class="text-4xl font-black text-primary">{{ number_format($tokensConsumed) }}</p>
        <p class="text-[10px] text-muted-foreground font-medium mt-2 uppercase tracking-tighter italic">Total Tokens Hashed</p>
        <i data-lucide="zap" class="absolute -right-4 -bottom-4 w-20 h-20 text-primary/5 group-hover:scale-110 transition-transform"></i>
    </div>

    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Identity Productivity</p>
        <p class="text-4xl font-black text-foreground">{{ $productivityIndex }}</p>
        <p class="text-[10px] text-muted-foreground font-medium mt-2 uppercase tracking-tighter italic">Assets per identity</p>
        <i data-lucide="users" class="absolute -right-4 -bottom-4 w-20 h-20 text-blue-500/5 group-hover:scale-110 transition-transform"></i>
    </div>

    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Intelligence Density</p>
        <p class="text-4xl font-black text-foreground">{{ $intelDensity }}</p>
        <p class="text-[10px] text-muted-foreground font-medium mt-2 uppercase tracking-tighter italic">Assets per research node</p>
        <i data-lucide="brain" class="absolute -right-4 -bottom-4 w-20 h-20 text-purple-500/5 group-hover:scale-110 transition-transform"></i>
    </div>
</div>
