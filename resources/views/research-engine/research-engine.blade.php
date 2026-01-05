@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{ 
    researchTitle: '',
    researchQuery: '',
    isResearching: false,
    showSuccessModal: false,
    createdResearchId: null,

    startResearch() {
        if (!this.researchTitle || !this.researchQuery) {
            alert('Please fill in both title and query.');
            return;
        }
        this.isResearching = true;
        fetch('{{ route('research-engine.start') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: this.researchTitle,
                query: this.researchQuery
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.createdResearchId = data.research.id;
                this.showSuccessModal = true;
                this.isResearching = false;
            } else {
                alert('Research failed: ' + (data.message || 'Unknown error'));
                this.isResearching = false;
            }
        })
        .catch(err => {
            console.error('Research Engine Error:', err);
            this.isResearching = false;
        });
    }
}">
    <div class="mb-12">
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Deep Research Protocol</h1>
        <p class="text-muted-foreground font-medium italic">
            Industrial-grade intelligence engine with real-time web grounding.
        </p>
    </div>

    <!-- Stats Cards -->
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

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
        <!-- New Research Form -->
        <div class="lg:col-span-2 space-y-8">
            <div class="rounded-[40px] border border-border bg-card p-10 shadow-xl relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                
                <div class="flex items-center gap-3 mb-8">
                    <i data-lucide="brain" class="w-6 h-6 text-primary"></i>
                    <h3 class="text-xl font-black uppercase tracking-tighter">Initiate Research</h3>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Research Title</label>
                        <input x-model="researchTitle" type="text" placeholder="e.g., Q3 Market Intelligence"
                               class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Analytical Query</label>
                        <textarea x-model="researchQuery" rows="8" placeholder="Describe the investigative parameters..."
                                  class="w-full bg-muted/20 border border-border rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all"></textarea>
                    </div>

                    <div class="pt-4">
                        <button @click="startResearch" :disabled="isResearching" 
                                class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                            <template x-if="!isResearching">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="search" class="w-5 h-5"></i>
                                    <span>Initiate Sweep</span>
                                </div>
                            </template>
                            <template x-if="isResearching">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                    <span>Grounding Data...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Research -->
        <div class="lg:col-span-3 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Protocol History</h3>
            <div class="grid grid-cols-1 gap-4">
                @forelse($recentResearches as $research)
                    <div class="p-6 bg-card border border-border rounded-[32px] hover:border-primary/30 transition-all group">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="font-black text-lg text-foreground uppercase tracking-tight">{{ $research->title }}</h3>
                                <p class="text-[10px] text-muted-foreground font-mono mt-1">{{ $research->created_at->diffForHumans() }}</p>
                            </div>
                            @php
                                $statusColors = [
                                    'completed' => 'text-green-600 bg-green-50 border-green-100',
                                    'researching' => 'text-amber-600 bg-amber-50 border-amber-100',
                                    'failed' => 'text-red-600 bg-red-50 border-red-100'
                                ];
                                $statusColor = $statusColors[$research->status] ?? 'text-slate-600 bg-slate-50 border-slate-100';
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg {{ $statusColor }} text-[9px] font-black uppercase tracking-widest border">
                                {{ $research->status }}
                            </span>
                        </div>

                        <div class="flex items-center gap-6 mb-6">
                            <div class="flex items-center gap-2">
                                <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $research->sources_count }} Sources</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $research->pages_count }} Pages</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('research-engine.show', $research) }}" 
                               class="flex-1 h-10 bg-primary text-primary-foreground rounded-xl flex items-center justify-center text-[9px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">
                                Preview Intelligence
                            </a>
                            <button @click="if(confirm('Purge this protocol record?')) { fetch('{{ route('research-engine.destroy', $research) }}', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => window.location.reload()); }"
                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-muted/5 rounded-[40px] border-2 border-dashed border-border opacity-50">
                        <i data-lucide="search" class="w-12 h-12 mx-auto mb-4 text-slate-300"></i>
                        <p class="text-sm font-medium italic">No active research protocols found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="showSuccessModal = false" class="bg-card w-full max-w-sm rounded-[40px] shadow-2xl border border-border p-10 text-center animate-in zoom-in-95 duration-300 relative overflow-hidden">
            <div class="absolute inset-0 bg-primary/5 pointer-events-none"></div>
            <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6 shadow-sm">
                <i data-lucide="check" class="w-10 h-10 text-green-600"></i>
            </div>
            <h2 class="text-2xl font-black text-foreground mb-2 uppercase tracking-tighter">Protocol Success</h2>
            <p class="text-muted-foreground mb-10 leading-relaxed text-sm font-medium italic">
                The research protocol has been finalized and grounded.
            </p>
            <div class="flex flex-col gap-3">
                <a :href="'/research-engine/' + createdResearchId" 
                   class="w-full h-14 rounded-2xl bg-primary text-primary-foreground font-black uppercase tracking-[0.2em] text-xs hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    Preview Intelligence
                </a>
                <button @click="showSuccessModal = false" class="w-full h-14 rounded-2xl bg-muted text-muted-foreground font-black uppercase tracking-[0.2em] text-xs hover:bg-muted/80 transition-all">Dismiss</button>
            </div>
        </div>
    </div>
</div>
@endsection
