@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Policy Architect</h1>
            <p class="text-muted-foreground font-medium italic">Construct and manage industrial-grade attribute-based access control (ABAC) nodes.</p>
        </div>
        <a href="{{ route('policies.create') }}" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="shield-plus" class="w-4 h-4"></i>
            Architect New Protocol
        </a>
    </div>

    <!-- Security Health Bar -->
    <div class="mb-12 p-6 rounded-[32px] bg-primary/5 border border-primary/10 flex items-center justify-between relative overflow-hidden group">
        <div class="flex items-center gap-6 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-widest">Active Grid Protection</h3>
                <p class="text-xs text-muted-foreground font-medium italic">Current policy stack: <span class="text-foreground font-bold">{{ $policies->count() }} active nodes</span>. All isolation boundaries verified.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 relative z-10">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-[9px] font-black uppercase text-green-500 tracking-widest">Isolation Secured</span>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
            <i data-lucide="shield" class="w-32 h-32 text-primary"></i>
        </div>
    </div>

    <!-- Policy Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($policies as $policy)
            <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
                <!-- Meta Badge -->
                <div class="flex items-center justify-between mb-8">
                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $policy->effect === 'allow' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                        {{ strtoupper($policy->effect) }} Protocol
                    </span>
                    <span class="mono text-[8px] text-slate-500 uppercase tracking-widest">Priority: {{ $policy->priority }}</span>
                </div>

                <!-- Asset Identity -->
                <div class="space-y-4 mb-8 flex-1">
                    <h3 class="text-xl font-black text-foreground truncate uppercase tracking-tight group-hover:text-primary transition-colors">{{ $policy->name }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @if(isset($policy->conditions['all']) || isset($policy->conditions['any']))
                            <span class="px-2 py-0.5 rounded bg-muted text-muted-foreground text-[8px] font-black uppercase tracking-widest">Complex Logic</span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-muted text-muted-foreground text-[8px] font-black uppercase tracking-widest">Atomic Node</span>
                        @endif
                        <span class="px-2 py-0.5 rounded bg-muted text-muted-foreground text-[8px] font-black uppercase tracking-widest">Attribute: {{ $policy->conditions['attribute'] ?? 'Compound' }}</span>
                    </div>
                </div>

                <!-- Footer Stats -->
                <div class="flex items-center justify-between pt-6 border-t border-border/50 mb-8">
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Deployed {{ $policy->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button class="flex-1 py-3 rounded-xl bg-muted/50 border border-border font-black uppercase text-[9px] tracking-widest hover:bg-white hover:text-black transition-all">Edit Protocol</button>
                    <form action="{{ route('policies.destroy', $policy) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Purge this security protocol?')" 
                                class="w-12 h-12 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <i data-lucide="shield-off" class="w-16 h-16 mx-auto text-slate-300"></i>
                <p class="text-sm font-medium">Your grid is using default RBAC fallback logic. No ABAC protocols architected.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection