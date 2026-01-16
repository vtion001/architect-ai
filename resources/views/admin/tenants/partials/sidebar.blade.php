{{-- Tenant Show - Sidebar Details --}}
<div class="space-y-6">
    @if(isset($linkedWaitlist))
    <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em]">Linked Lead History</h3>
    <div class="bg-slate-900 border border-amber-500/20 rounded-3xl p-6 space-y-4 relative overflow-hidden">
        <div class="absolute inset-0 bg-amber-500/5"></div>
        <div class="relative z-10">
            <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Origin Lead</p>
            <p class="text-sm font-bold text-white truncate">{{ $linkedWaitlist->email }}</p>
        </div>
        <div class="relative z-10">
            <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Acquisition Date</p>
            <p class="text-xs font-mono text-amber-400">{{ $linkedWaitlist->created_at->format('M d, Y') }}</p>
        </div>
        <div class="relative z-10">
            <span class="px-2 py-0.5 rounded border border-amber-500/30 bg-amber-500/10 text-amber-500 text-[9px] font-black uppercase tracking-widest">
                Successfully Converted
            </span>
        </div>
    </div>
    @endif

    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Tenant DNA</h3>
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6">
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Status</p>
            <span class="px-2 py-0.5 rounded-full bg-green-500/10 text-green-500 text-[10px] font-bold border border-green-500/20">{{ $tenant->status }}</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Type</p>
            <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-500 text-[10px] font-bold border border-blue-500/20">{{ strtoupper($tenant->type) }}</span>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Unique ID</p>
            <p class="text-xs font-mono text-white">{{ $tenant->id }}</p>
        </div>
    </div>

    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Resource Economy</h3>
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Current Balance</p>
                <p class="text-3xl font-black text-cyan-500">{{ number_format($tokenBalance) }}</p>
            </div>
            <button @click="showGrantModal = true" class="w-10 h-10 bg-cyan-500/10 border border-cyan-500/20 rounded-xl flex items-center justify-center text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all">
                <i data-lucide="plus" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-3">
            <p class="text-[10px] font-black text-slate-500 uppercase">Recent Transactions</p>
            <div class="space-y-2 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                @foreach($transactions as $tx)
                    <div class="p-2.5 rounded-xl bg-slate-950/50 border border-slate-800">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[9px] font-bold {{ $tx->amount > 0 ? 'text-green-500' : 'text-red-500' }}">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </span>
                            <span class="text-[8px] text-slate-600 uppercase">{{ $tx->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium truncate">{{ $tx->reason }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
