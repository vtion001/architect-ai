{{-- God View - Protocol Registry Table --}}
<div class="lg:col-span-8 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Cross-Grid Protocol Registry</h3>
    <div class="bg-slate-900 border border-slate-800 rounded-[40px] overflow-hidden shadow-2xl">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-950/50 text-slate-500 font-black uppercase tracking-widest border-b border-slate-800">
                <tr>
                    <th class="p-6">Origin Node</th>
                    <th class="p-6">Identity</th>
                    <th class="p-6">Protocol Action</th>
                    <th class="p-6 text-right">Registry Cycle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($globalAudit as $log)
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-300 uppercase tracking-tight">{{ $log->tenant?->name ?? 'GRID_CORE' }}</span>
                                <span class="text-[8px] font-mono text-slate-600 uppercase">{{ substr($log->tenant_id ?? 'root', 0, 13) }}</span>
                            </div>
                        </td>
                        <td class="p-6">
                            <span class="text-slate-400 font-medium">{{ $log->actor?->email ?? 'SYSTEM' }}</span>
                        </td>
                        <td class="p-6 font-mono text-blue-400 font-bold tracking-tighter uppercase">
                            {{ $log->action }}
                        </td>
                        <td class="p-6 text-right text-slate-500 font-medium italic">
                            {{ $log->timestamp->diffForHumans() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
