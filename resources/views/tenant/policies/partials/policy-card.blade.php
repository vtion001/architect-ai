{{-- Policies Index - Policy Card --}}
<div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
    <!-- Meta Badge -->
    <div class="flex items-center justify-between mb-8">
        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $policy->effect === 'allow' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }}">
            {{ strtoupper($policy->effect) }} Protocol
        </span>
        <span class="mono text-[8px] text-slate-500 uppercase tracking-widest">Priority: {{ $policy->priority }}</span>
    </div>

    <!-- Asset Identity -->
    <div class="space-y-4 mb-8 flex-1">
        <h3 class="text-xl font-black text-foreground truncate uppercase tracking-tight group-hover:text-primary transition-colors">{{ $policy->name }}</h3>
        <div class="flex flex-wrap gap-2">
            @if(isset($policy->conditions['all']) || isset($policy->conditions['any']))
                <span class="px-2 py-0.5 rounded bg-muted text-muted-foreground text-[8px] font-black uppercase tracking-widest">Complex Logic</span>
            @else
                <span class="px-2 py-0.5 rounded bg-muted text-muted-foreground text-[8px] font-black uppercase tracking-widest">Atomic Node</span>
            @endif
            <span class="px-2 py-0.5 rounded bg-muted text-muted-foreground text-[8px] font-black uppercase tracking-widest">Attribute: {{ $policy->conditions['attribute'] ?? 'Compound' }}</span>
        </div>
    </div>

    <!-- Footer Stats -->
    <div class="flex items-center justify-between pt-6 border-t border-border/50 mb-8">
        <div class="flex items-center gap-2">
            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
            <span class="text-[10px] font-bold text-slate-500 uppercase">Deployed {{ $policy->created_at->diffForHumans() }}</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-2">
        <button class="flex-1 py-3 rounded-xl bg-muted/50 border border-border font-black uppercase text-[9px] tracking-widest hover:bg-white hover:text-black transition-all">Edit Protocol</button>
        <form action="{{ route('policies.destroy', $policy) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Purge this security protocol?')" 
                    class="w-12 h-12 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </form>
    </div>
</div>
