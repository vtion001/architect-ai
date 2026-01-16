{{-- Admin Dashboard - Audit Log Table --}}
<div class="lg:col-span-2 space-y-6">
    <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.2em] px-1">Global Audit Pulse</h3>
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950/50 text-slate-500 font-black uppercase tracking-widest border-b border-slate-800">
                <tr>
                    <th class="p-4 px-6">Identity</th>
                    <th class="p-4">Tenant</th>
                    <th class="p-4">Action</th>
                    <th class="p-4">Result</th>
                    <th class="p-4 text-right">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($recentLogs as $log)
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="p-4 px-6">
                            <p class="font-bold text-slate-300">{{ $log->actor?->email ?? 'SYSTEM' }}</p>
                            <p class="text-[9px] font-mono text-slate-600 uppercase">{{ substr($log->actor_id ?? 'root', 0, 8) }}</p>
                        </td>
                        <td class="p-4 font-mono text-slate-400">{{ $log->tenant?->name ?? 'GRID' }}</td>
                        <td class="p-4 font-bold uppercase text-slate-200 tracking-tight">{{ $log->action }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded-full font-black text-[9px] uppercase tracking-widest {{ $log->result === 'success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                                {{ $log->result }}
                            </span>
                        </td>
                        <td class="p-4 text-right text-slate-500">{{ $log->timestamp->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
