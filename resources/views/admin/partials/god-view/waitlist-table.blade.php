{{-- God View - Waitlist Registry Table --}}
<div class="space-y-6">
    <div class="flex items-center justify-between px-1">
        <div class="flex items-center gap-4">
            <div class="w-2 h-8 bg-amber-500 rounded-full"></div>
            <div>
                <h3 class="text-xl font-black text-white uppercase tracking-tighter">Master Waitlist Registry</h3>
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest italic">Global user acquisition queue</p>
            </div>
        </div>
        <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20">Authorized Master Node Only</span>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-[40px] overflow-hidden shadow-2xl">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-950/50 text-slate-500 font-black uppercase tracking-widest border-b border-slate-800">
                <tr>
                    <th class="p-6">Lead Identity</th>
                    <th class="p-6">Agency / Brand</th>
                    <th class="p-6">Status</th>
                    <th class="p-6 text-right">Acquisition Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($waitlistEntries as $entry)
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500 uppercase">
                                    {{ substr($entry->name ?? $entry->email, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-300 uppercase tracking-tight">{{ $entry->name ?? 'Anonymous Identity' }}</span>
                                    <div class="flex items-center gap-2 group/copy cursor-pointer" onclick="navigator.clipboard.writeText('{{ $entry->email }}'); alert('Identity Hashed to Clipboard');">
                                        <span class="text-[9px] text-slate-500 font-medium italic group-hover/copy:text-primary transition-colors">{{ $entry->email }}</span>
                                        <i data-lucide="copy" class="w-2.5 h-3 text-slate-600 group-hover/copy:text-primary transition-colors opacity-0 group-hover/copy:opacity-100"></i>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <span class="text-slate-400 font-medium uppercase tracking-widest text-[10px]">{{ $entry->agency_name ?? 'Individual Node' }}</span>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $entry->status === 'pending' ? 'bg-amber-500 animate-pulse' : 'bg-green-500' }}"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest {{ $entry->status === 'pending' ? 'text-amber-500' : 'text-green-500' }}">
                                    {{ $entry->status }}
                                </span>
                            </div>
                        </td>
                        <td class="p-6 text-right text-slate-500 font-medium italic">
                            {{ $entry->created_at->format('M d, Y • H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center opacity-30 italic text-slate-500">
                            No master leads found in the grid queue.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
