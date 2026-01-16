{{-- Billing & Treasury Tab --}}
@props(['tokenBalance', 'auditLogs'])

<div x-show="activeTab === 'billing'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
    <h2 class="text-2xl font-black uppercase tracking-tighter mb-8">Resource Treasury</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="p-8 rounded-[32px] bg-primary/5 border border-primary/10 relative overflow-hidden group">
            <p class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-4">Current Balance</p>
            <p class="text-5xl font-black text-primary">{{ number_format($tokenBalance) }}</p>
            <div class="mt-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[9px] font-bold text-slate-500 uppercase">Treasury Node Healthy</span>
            </div>
            <i data-lucide="coins" class="absolute -right-4 -bottom-4 w-24 h-24 text-primary/5 group-hover:scale-110 transition-transform"></i>
        </div>
        <div class="p-8 rounded-[32px] border border-border flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Usage Analysis</p>
                <p class="text-sm font-medium text-muted-foreground leading-relaxed italic">Your resource consumption has been stable. Automated top-ups are disabled.</p>
            </div>
            <button class="w-full h-12 bg-white text-black rounded-xl font-black uppercase text-[9px] tracking-widest shadow-lg hover:bg-primary hover:text-white transition-all">Acquire Tokens</button>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Registry Audit Trail</h3>
        <div class="bg-muted/10 border border-border rounded-3xl overflow-hidden">
            <table class="w-full text-left text-[10px]">
                <thead class="bg-muted/50 border-b border-border text-slate-500 font-black uppercase tracking-widest">
                    <tr>
                        <th class="p-4 px-6">Timestamp</th>
                        <th class="p-4">Action Protocol</th>
                        <th class="p-4">Identity</th>
                        <th class="p-4 text-right">Result</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @foreach($auditLogs as $log)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="p-4 px-6 font-medium text-slate-500">{{ $log->timestamp->format('Y-m-d H:i') }}</td>
                            <td class="p-4 font-bold text-foreground uppercase tracking-tight">{{ $log->action }}</td>
                            <td class="p-4 text-slate-500 italic">{{ $log->actor?->email ?? 'SYSTEM' }}</td>
                            <td class="p-4 text-right">
                                <span class="px-2 py-0.5 rounded-md font-black uppercase tracking-widest {{ $log->result === 'success' ? 'text-green-500 bg-green-500/5' : 'text-red-500 bg-red-500/5' }}">
                                    {{ $log->result }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
