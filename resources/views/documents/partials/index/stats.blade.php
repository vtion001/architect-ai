{{-- Documents Index - Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
        <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
            <i data-lucide="folder" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Archived Assets</p>
            <p class="text-2xl font-black text-white">{{ $stats['total_assets'] }}</p>
        </div>
    </div>
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
        <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500">
            <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Reports Generated</p>
            <p class="text-2xl font-black text-white">{{ $stats['report_count'] }}</p>
        </div>
    </div>
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
        <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500">
            <i data-lucide="hard-drive" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Registry Depth</p>
            <p class="text-2xl font-black text-white">{{ $stats['storage_used'] }}</p>
        </div>
    </div>
</div>
