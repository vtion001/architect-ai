{{-- Dashboard Metrics Grid --}}
@props(['contentCount', 'researchCount', 'tokenBalance'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    
    {{-- Metric 1: Hours Saved (Value Prop) --}}
    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Efficiency</span>
        </div>
        {{-- Calculating approx 30 mins saved per content piece --}}
        <p class="text-4xl font-black text-foreground">{{ number_format($contentCount * 0.5, 1) }}h</p>
        <p class="text-[10px] font-bold text-emerald-500 uppercase mt-2 italic">Time Saved This Month</p>
    </div>

    {{-- Metric 2: Content Output --}}
    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover:bg-primary group-hover:text-black transition-all">
                <i data-lucide="files" class="w-6 h-6"></i>
            </div>
            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Production</span>
        </div>
        <p class="text-4xl font-black text-foreground">{{ $contentCount }}</p>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Assets Generated</p>
    </div>

    {{-- Metric 3: Research Depth --}}
    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 group-hover:bg-blue-500 group-hover:text-white transition-all">
                <i data-lucide="brain-circuit" class="w-6 h-6"></i>
            </div>
            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Intelligence</span>
        </div>
        <p class="text-4xl font-black text-foreground">{{ $researchCount }}</p>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Research Sessions</p>
    </div>

    {{-- Metric 4: Treasury --}}
    <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 border border-cyan-500/20 group-hover:bg-cyan-500 group-hover:text-black transition-all">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Credits</span>
        </div>
        <p class="text-4xl font-black text-foreground">{{ number_format($tokenBalance) }}</p>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Available Tokens</p>
    </div>
</div>
