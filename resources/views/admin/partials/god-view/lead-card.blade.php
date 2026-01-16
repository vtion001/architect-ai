{{-- God View - Lead Card --}}
<div class="p-5 rounded-3xl bg-slate-950/50 border border-slate-800 hover:border-cyan-500/30 transition-all group">
    <div class="flex items-center justify-between mb-4">
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 font-black">
            {{ substr($entry->email, 0, 1) }}
        </div>
        <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded border {{ $entry->status === 'pending' ? 'text-amber-500 border-amber-500/20' : 'text-green-500 border-green-500/20' }}">
            {{ $entry->status }}
        </span>
    </div>
    <div class="mb-4">
        <p class="text-xs font-bold text-white truncate">{{ $entry->email }}</p>
        <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mt-1">{{ $entry->agency_name ?? 'Individual Node' }}</p>
    </div>
    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
        @if($entry->status === 'pending')
            <button @click="convertLead('{{ $entry->id }}')" :disabled="isConverting" class="flex-1 h-9 bg-cyan-500 text-black rounded-xl font-black uppercase text-[8px] tracking-widest hover:bg-white transition-all disabled:opacity-50">
                <span x-show="!isConverting">Provision</span>
                <span x-show="isConverting">...</span>
            </button>
        @else
            <span class="flex-1 text-center py-2 text-[8px] font-black uppercase text-slate-600">Protocol Active</span>
        @endif
        <button class="w-9 h-9 bg-slate-800 text-slate-400 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
    </div>
</div>
