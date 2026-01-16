{{-- God View - LLMOps System Vitality --}}
@if(isset($llmHealth))
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Status Monitor -->
    <div class="lg:col-span-3 bg-slate-900 border border-slate-800 rounded-[32px] p-8 shadow-xl flex flex-col md:flex-row justify-between items-center gap-8">
        <div class="flex items-center gap-6 w-full md:w-auto">
            <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-400 border border-slate-700">
                <i data-lucide="terminal" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-white uppercase tracking-tight">LLMOps Master Monitor</h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-green-500 uppercase tracking-widest">Master Node: Operational</span>
                </div>
            </div>
        </div>
        
        <div class="flex-1 w-full grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">OpenAI Gateway</p>
                <p class="text-sm font-bold text-emerald-400">{{ $llmHealth['api_status'] ?? 'N/A' }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Vector Index</p>
                <p class="text-sm font-bold text-blue-400">{{ $llmHealth['vector_db_status'] ?? 'N/A' }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Queue Workers</p>
                <p class="text-sm font-bold text-white">{{ $llmHealth['active_workers'] ?? 0 }} Up</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center group/err cursor-pointer hover:border-red-500/50 transition-colors">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1 group-hover/err:text-red-500">Log Errors</p>
                <p class="text-sm font-bold {{ ($llmHealth['error_rate'] ?? 0) > 0 ? 'text-red-500' : 'text-slate-400' }}">{{ $llmHealth['error_rate'] ?? 0 }} Failed</p>
            </div>
        </div>
    </div>

    <!-- Token Burn Rate -->
    <div class="bg-slate-900 border border-slate-800 rounded-[32px] p-8 shadow-xl flex flex-col justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-orange-500/5"></div>
        <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest mb-2">24h Token Burn</p>
        <p class="text-4xl font-black text-white">{{ number_format($llmHealth['tokens_burned_24h'] ?? 0) }}</p>
        <p class="text-[10px] text-slate-500 mt-2 font-medium italic">Resource Consumption Rate</p>
    </div>
</div>
@endif
