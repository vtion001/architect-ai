@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto animate-in fade-in duration-700">
    <!-- Grid Identification Header -->
    <div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground mb-2">Agency Grid Overview</h1>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Protocol Sync: {{ $gridStatus }}</span>
                </div>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-tighter italic">Workspace Node: {{ app(\App\Models\Tenant::class)->slug }}</span>
            </div>
        </div>
        <div class="flex gap-4">
            <button @click="showCommandPalette = true" class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
                <i data-lucide="command" class="w-4 h-4"></i>
                Command Palette
            </button>
            <a href="/research-engine" class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                <i data-lucide="zap" class="w-4 h-4"></i>
                Initiate Protocol
            </a>
        </div>
    </div>

    <!-- Core Intelligence Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 group-hover:bg-blue-500 group-hover:text-white transition-all">
                    <i data-lucide="search" class="w-6 h-6"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Research Node</span>
            </div>
            <p class="text-4xl font-black text-foreground">{{ $researchCount }}</p>
            <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Intelligence Sessions</p>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="search" class="w-24 h-24 text-blue-500"></i>
            </div>
        </div>

        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover:bg-primary group-hover:text-black transition-all">
                    <i data-lucide="pencil" class="w-6 h-6"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Architect Node</span>
            </div>
            <p class="text-4xl font-black text-foreground">{{ $contentCount }}</p>
            <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Assets Provisions</p>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="pencil" class="w-24 h-24 text-primary"></i>
            </div>
        </div>

        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500 border border-purple-500/20 group-hover:bg-purple-500 group-hover:text-white transition-all">
                    <i data-lucide="share-2" class="w-6 h-6"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Social Node</span>
            </div>
            <p class="text-4xl font-black text-foreground">{{ $socialCount }}</p>
            <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Platform Deployments</p>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="share-2" class="w-24 h-24 text-purple-500"></i>
            </div>
        </div>

        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 border border-cyan-500/20 group-hover:bg-cyan-500 group-hover:text-black transition-all">
                    <i data-lucide="coins" class="w-6 h-6"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Treasury Node</span>
            </div>
            <p class="text-4xl font-black text-primary">{{ number_format($tokenBalance) }}</p>
            <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Grid Credits</p>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="coins" class="w-24 h-24 text-cyan-500"></i>
            </div>
        </div>
    </div>

    <!-- Telemetry Visualization & Knowledge -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        <div class="lg:col-span-8 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Network Intensity Heatmap</h3>
            <div class="bg-card border border-border rounded-[40px] p-10 h-[400px] flex flex-col justify-between group">
                <div class="flex-1 flex items-end gap-6">
                    @foreach($intensityData as $data)
                        <div class="flex-1 flex flex-col items-center gap-4 group/bar">
                            <div class="w-full bg-muted/20 rounded-2xl relative overflow-hidden transition-all group-hover/bar:bg-primary/5 border border-transparent group-hover/bar:border-primary/20" 
                                 style="height: {{ max(10, ($data['value'] / max(1, max(array_column($intensityData, 'value')))) * 100) }}%">
                                <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent opacity-0 group-hover/bar:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="mono text-[10px] font-black text-slate-500 uppercase tracking-widest group-hover/bar:text-primary transition-colors">{{ $data['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Intelligence Assets</h3>
            <div class="bg-card border border-border rounded-[40px] p-8 h-[400px] flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                
                <div class="space-y-6 flex-1">
                    <div class="w-16 h-16 rounded-3xl bg-muted border border-border flex items-center justify-center text-primary">
                        <i data-lucide="database" class="w-8 h-8"></i>
                    </div>
                    <div class="space-y-2">
                        <h4 class="text-2xl font-black uppercase tracking-tight">RAG Context</h4>
                        <p class="text-xs text-muted-foreground font-medium italic leading-relaxed">
                            Your grid currently stores <span class="text-foreground font-bold">{{ $kbCount }} validated intelligence assets</span>. These documents are used to ground all AI generations in your agency's proprietary data.
                        </p>
                    </div>
                </div>

                <a href="/knowledge-base" class="w-full h-14 bg-muted border border-border rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-white hover:text-black transition-all flex items-center justify-center gap-3">
                    Access Hub
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Industrial Activity Registry -->
    <div class="space-y-6">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Global Activity Registry</h3>
        <div class="bg-card border border-border rounded-[40px] overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-muted/50 text-slate-500 font-black uppercase tracking-widest border-b border-border">
                    <tr>
                        <th class="p-6">Execution Identity</th>
                        <th class="p-6">Active Node</th>
                        <th class="p-6">Protocol Action</th>
                        <th class="p-6">Contextual Snippet</th>
                        <th class="p-6 text-right">Registry Cycle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @foreach($recentActivities as $activity)
                        <tr class="hover:bg-muted/30 transition-colors group">
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-muted border border-border flex items-center justify-center text-[10px] font-black text-slate-500 uppercase">
                                        {{ substr($activity['actor'], 0, 1) }}
                                    </div>
                                    <span class="font-bold text-foreground">{{ $activity['actor'] }}</span>
                                </div>
                            </td>
                            <td class="p-6">
                                <span class="px-2.5 py-1 rounded-lg bg-muted text-muted-foreground text-[9px] font-black uppercase tracking-widest border border-border">
                                    {{ $activity['node'] }}
                                </span>
                            </td>
                            <td class="p-6 font-mono text-primary font-bold tracking-tighter">
                                {{ $activity['protocol'] }}
                            </td>
                            <td class="p-6 text-slate-500 italic truncate max-w-xs">
                                "{{ $activity['context'] }}"
                            </td>
                            <td class="p-6 text-right text-slate-400 font-medium">
                                {{ $activity['time'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if(empty($recentActivities))
                <div class="p-20 text-center opacity-30 italic">
                    <i data-lucide="clipboard-list" class="w-12 h-12 mx-auto mb-4"></i>
                    <p class="text-sm font-bold uppercase tracking-widest">Registry empty</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection