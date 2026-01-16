{{-- Sub-Accounts - Account Card --}}
<div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
    <!-- Status & ID -->
    <div class="flex items-center justify-between mb-8">
        <span class="px-2.5 py-1 rounded-lg bg-green-50 text-green-600 text-[9px] font-black uppercase tracking-widest border border-green-100">
            {{ $sub->status }}
        </span>
        <span class="mono text-[8px] text-slate-400 uppercase tracking-widest">Node: {{ substr($sub->id, 0, 8) }}</span>
    </div>

    <!-- Identity -->
    <div class="space-y-4 mb-8">
        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black text-xl border border-primary/20 group-hover:bg-primary group-hover:text-black transition-all">
            {{ substr($sub->name, 0, 1) }}
        </div>
        <div>
            <h3 class="text-xl font-black text-foreground truncate uppercase tracking-tight">{{ $sub->name }}</h3>
            <p class="text-xs text-muted-foreground font-mono">/{{ $sub->slug }}</p>
        </div>
    </div>

    <!-- Resource Telemetry -->
    <div class="grid grid-cols-2 gap-4 pt-6 border-t border-border/50">
        <div>
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Identity Count</p>
            <p class="text-lg font-bold text-foreground">{{ $sub->users_count }}</p>
        </div>
        <div>
            <p class="text-[9px] font-black text-primary uppercase tracking-widest mb-1">Treasury Balance</p>
            <p class="text-lg font-black text-primary">{{ number_format($sub->token_balance) }}</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-8 pt-6">
        <button @click="selectedSub = @js($sub); showImpersonateModal = true" 
                class="w-full py-3 rounded-xl border border-border bg-muted/5 font-black uppercase text-[10px] tracking-widest text-muted-foreground hover:bg-primary hover:text-white hover:border-primary transition-all">
            Enter Workspace
        </button>
    </div>
</div>
