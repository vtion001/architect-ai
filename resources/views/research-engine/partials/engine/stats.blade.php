{{-- Research Engine - Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <div class="rounded-[32px] border border-border bg-card p-6 shadow-sm relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                <i data-lucide="file-text" class="w-5 h-5"></i>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reports</span>
        </div>
        <p class="text-3xl font-black text-white">{{ number_format($stats['total_reports']) }}</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Total Architected</p>
    </div>
    
    <div class="rounded-[32px] border border-border bg-card p-6 shadow-sm relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Processing</span>
        </div>
        <p class="text-3xl font-black text-white">{{ $stats['active_research'] }}</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Active Protocols</p>
    </div>

    <div class="rounded-[32px] border border-border bg-card p-6 shadow-sm relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-500">
                <i data-lucide="globe" class="w-5 h-5"></i>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Grounding</span>
        </div>
        <p class="text-3xl font-black text-white">{{ number_format($stats['sources_analyzed']) }}</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Sources Indexed</p>
    </div>

    <div class="rounded-[32px] border border-border bg-card p-6 shadow-sm relative overflow-hidden group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                <i data-lucide="zap" class="w-5 h-5"></i>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Accuracy</span>
        </div>
        <p class="text-3xl font-black text-white">{{ $stats['success_rate'] }}%</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Protocol Success</p>
    </div>
</div>
