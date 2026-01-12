@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto animate-in fade-in duration-700">
    
    <!-- Welcome Header -->
    <div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground mb-2">Command Center</h1>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">System Online</span>
                </div>
                <!-- Brand Context Indicator -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-muted/50 border border-border">
                    <i data-lucide="fingerprint" class="w-3 h-3 text-primary"></i>
                    <span class="text-[10px] font-mono text-foreground uppercase tracking-tight">
                        Active Workspace: <span class="text-primary font-bold">{{ app(\App\Models\Tenant::class)->slug }}</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex gap-4">
            <!-- Quick Action: New Brand -->
            <a href="/settings/brands" class="h-14 px-6 rounded-2xl border border-border bg-card font-bold uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Brand DNA
            </a>
            <a href="/content-creator" class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                <i data-lucide="zap" class="w-4 h-4"></i>
                Create Content
            </a>
        </div>
    </div>

    <!-- ROI & Intelligence Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        
        <!-- Metric 1: Hours Saved (Value Prop) -->
        <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Efficiency</span>
            </div>
            <!-- Calculating approx 30 mins saved per content piece -->
            <p class="text-4xl font-black text-foreground">{{ number_format($contentCount * 0.5, 1) }}h</p>
            <p class="text-[10px] font-bold text-emerald-500 uppercase mt-2 italic">Time Saved This Month</p>
        </div>

        <!-- Metric 2: Content Output -->
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

        <!-- Metric 3: Research Depth -->
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

        <!-- Metric 4: Treasury -->
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

    <!-- Quick Start: Brand DNA & Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        <!-- Main Action Area -->
        <div class="lg:col-span-8 space-y-6">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Quick Actions</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Action 1: Define Brand -->
                <a href="/settings/brands" class="group relative bg-card hover:bg-muted/50 border border-border rounded-[32px] p-8 transition-all overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                        <i data-lucide="fingerprint" class="w-32 h-32 text-primary"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-6">
                            <i data-lucide="scan-face" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-xl font-black uppercase tracking-tight mb-2">Define Brand DNA</h4>
                        <p class="text-xs text-muted-foreground font-medium leading-relaxed mb-6">
                            Upload a website or PDF to extract tone, values, and audience profiles.
                        </p>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                            Start Analysis <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </span>
                    </div>
                </a>

                <!-- Action 2: Research -->
                <a href="/research-engine" class="group relative bg-card hover:bg-muted/50 border border-border rounded-[32px] p-8 transition-all overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                        <i data-lucide="globe" class="w-32 h-32 text-blue-500"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 mb-6">
                            <i data-lucide="search" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-xl font-black uppercase tracking-tight mb-2">Deep Research</h4>
                        <p class="text-xs text-muted-foreground font-medium leading-relaxed mb-6">
                            Analyze competitors or topics to find trending angles for your content.
                        </p>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-blue-500 flex items-center gap-2">
                            Begin Research <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Sidebar: Knowledge Status -->
        <div class="lg:col-span-4 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Knowledge Base</h3>
            <div class="bg-card border border-border rounded-[32px] p-8 h-full flex flex-col relative overflow-hidden">
                <div class="flex-1 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-muted border border-border flex items-center justify-center text-foreground">
                            <i data-lucide="database" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black">{{ $kbCount }}</div>
                            <div class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Documents Indexed</div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        @if($kbCount > 0)
                            <div class="p-3 bg-muted/30 rounded-xl border border-border flex items-center gap-3">
                                <i data-lucide="file-check" class="w-4 h-4 text-green-500"></i>
                                <span class="text-xs font-medium text-slate-400">Context Active</span>
                            </div>
                        @else
                            <div class="p-3 bg-yellow-500/10 rounded-xl border border-yellow-500/20 flex items-center gap-3">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-yellow-500"></i>
                                <span class="text-xs font-medium text-yellow-500">No context found. Upload docs to improve AI.</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <a href="/knowledge-base" class="mt-8 w-full h-12 bg-muted hover:bg-muted/80 border border-border rounded-xl font-bold uppercase text-[10px] tracking-widest flex items-center justify-center gap-2 transition-all">
                    Manage Documents
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="space-y-6">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Live Activity Stream</h3>
        <div class="bg-card border border-border rounded-[32px] overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs">
                <thead class="bg-muted/50 text-slate-500 font-black uppercase tracking-widest border-b border-border">
                    <tr>
                        <th class="p-6">User</th>
                        <th class="p-6">Action</th>
                        <th class="p-6">Details</th>
                        <th class="p-6 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @foreach($recentActivities as $activity)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="p-6 font-bold text-foreground">{{ $activity['actor'] }}</td>
                            <td class="p-6">
                                <span class="px-2 py-1 rounded bg-muted border border-border text-[9px] font-bold uppercase tracking-widest">
                                    {{ $activity['protocol'] }}
                                </span>
                            </td>
                            <td class="p-6 text-slate-500 italic">{{ $activity['context'] }}</td>
                            <td class="p-6 text-right text-slate-400 font-mono">{{ $activity['time'] }}</td>
                        </tr>
                    @endforeach
                    @if(empty($recentActivities))
                        <tr>
                            <td colspan="4" class="p-12 text-center text-slate-500 italic">No activity recorded yet.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection