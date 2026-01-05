@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Grid Telemetry Command</h1>
            <p class="text-muted-foreground font-medium italic">High-resolution analysis of identity productivity, resource intensity, and grid intelligence.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex flex-col items-end">
                <span class="text-[8px] font-black uppercase text-slate-500 tracking-[0.2em]">Node Status</span>
                <span class="text-[10px] font-bold text-green-500 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                    Grid Sync: 100%
                </span>
            </div>
        </div>
    </div>

    <!-- Core Grid Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Registry Success</p>
            <p class="text-4xl font-black text-foreground">{{ $successRate }}%</p>
            <div class="mt-6 w-full bg-muted h-1 rounded-full overflow-hidden">
                <div class="bg-green-500 h-full" style="width: {{ $successRate }}%"></div>
            </div>
            <i data-lucide="shield-check" class="absolute -right-4 -bottom-4 w-20 h-20 text-green-500/5 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Treasury Output</p>
            <p class="text-4xl font-black text-primary">{{ number_format($tokensConsumed) }}</p>
            <p class="text-[10px] text-muted-foreground font-medium mt-2 uppercase tracking-tighter italic">Total Tokens Hashed</p>
            <i data-lucide="zap" class="absolute -right-4 -bottom-4 w-20 h-20 text-primary/5 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Identity Productivity</p>
            <p class="text-4xl font-black text-foreground">{{ $productivityIndex }}</p>
            <p class="text-[10px] text-muted-foreground font-medium mt-2 uppercase tracking-tighter italic">Assets per identity</p>
            <i data-lucide="users" class="absolute -right-4 -bottom-4 w-20 h-20 text-blue-500/5 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Intelligence Density</p>
            <p class="text-4xl font-black text-foreground">{{ $intelDensity }}</p>
            <p class="text-[10px] text-muted-foreground font-medium mt-2 uppercase tracking-tighter italic">Assets per research node</p>
            <i data-lucide="brain" class="absolute -right-4 -bottom-4 w-20 h-20 text-purple-500/5 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    <!-- Telemetry Visualization -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Consumption Intensity (Last 7 Days)</h3>
            <div class="bg-card border border-border rounded-[40px] p-10 h-[400px] flex flex-col justify-between">
                <div class="flex-1 flex items-end gap-4">
                    @foreach($intensityTrend as $idx => $val)
                        <div class="flex-1 flex flex-col items-center gap-4 group">
                            <div class="w-full bg-muted/20 rounded-xl relative overflow-hidden transition-all group-hover:bg-primary/5 border border-transparent group-hover:border-primary/20" 
                                 style="height: {{ max(10, ($val / max(1, max($intensityTrend))) * 100) }}%">
                                <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="mono text-[9px] font-black text-slate-500 uppercase tracking-widest group-hover:text-primary transition-colors">{{ $labels[$idx] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Active Grid Nodes</h3>
            <div class="bg-card border border-border rounded-[40px] p-8 space-y-6 shadow-sm">
                <div class="flex items-center justify-between p-4 rounded-2xl bg-muted/20 border border-border group hover:border-primary/30 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-foreground">Research Node</p>
                            <p class="text-[9px] font-bold text-slate-500">Protocol: Gemini-Flash</p>
                        </div>
                    </div>
                    <span class="text-[8px] font-black text-green-500 uppercase tracking-widest">Active</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-2xl bg-muted/20 border border-border group hover:border-primary/30 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <i data-lucide="pencil" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-foreground">Architect Node</p>
                            <p class="text-[9px] font-bold text-slate-500">Protocol: GPT-4o-Mini</p>
                        </div>
                    </div>
                    <span class="text-[8px] font-black text-green-500 uppercase tracking-widest">Active</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-2xl bg-muted/20 border border-border group hover:border-primary/30 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                            <i data-lucide="share-2" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-foreground">Social Hub</p>
                            <p class="text-[9px] font-bold text-slate-500">Protocol: Meta Graph</p>
                        </div>
                    </div>
                    <span class="text-[8px] font-black text-green-500 uppercase tracking-widest">Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategic Intelligence Feed -->
    <div class="space-y-6">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Global Intelligence Flow</h3>
        <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <i data-lucide="trending-up" class="w-5 h-5 text-primary"></i>
                        <h4 class="text-sm font-black uppercase tracking-tight">Growth Indicators</h4>
                    </div>
                    <p class="text-xs font-medium text-muted-foreground leading-relaxed italic">
                        The grid has seen a <span class="text-foreground font-bold">12.4% increase</span> in intelligence density over the last 7 cycles. Identity productivity is currently performing at peak baseline efficiency.
                    </p>
                    <div class="pt-4 flex gap-4">
                        <div class="flex-1 p-4 rounded-2xl border border-border bg-muted/10">
                            <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-1">Delta Identity</p>
                            <p class="text-lg font-bold text-foreground">+2.1%</p>
                        </div>
                        <div class="flex-1 p-4 rounded-2xl border border-border bg-muted/10">
                            <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-1">Delta Intelligence</p>
                            <p class="text-lg font-bold text-foreground">+8.5%</p>
                        </div>
                    </div>
                </div>
                <div class="h-full bg-muted/10 rounded-3xl border border-border flex items-center justify-center p-8 text-center relative overflow-hidden">
                    <div class="absolute inset-0 grid-canvas pointer-events-none opacity-20"></div>
                    <div>
                        <i data-lucide="bar-chart-2" class="w-12 h-12 mx-auto mb-4 text-slate-300 opacity-20"></i>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Strategic Insight Generation Active</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
