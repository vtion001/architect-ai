{{-- Dashboard Welcome Header --}}
<div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
    <div>
        <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground mb-2">Command Center</h1>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">System Online</span>
            </div>
            {{-- Brand Context Indicator --}}
            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-muted/50 border border-border">
                <i data-lucide="fingerprint" class="w-3 h-3 text-primary"></i>
                <span class="text-[10px] font-mono text-foreground uppercase tracking-tight">
                    Active Workspace: <span class="text-primary font-bold">{{ app(\App\Models\Tenant::class)->slug }}</span>
                </span>
            </div>
        </div>
    </div>
    <div class="flex gap-4">
        {{-- Quick Action: New Brand --}}
        <a href="/settings/brands" class="h-14 px-6 rounded-2xl border border-border bg-card font-bold uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Brand DNA
        </a>
        <a href="/content-creator" class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
            <i data-lucide="zap" class="w-4 h-4"></i>
            Create Content
        </a>
    </div>
</div>
