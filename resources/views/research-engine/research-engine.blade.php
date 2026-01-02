@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{ 
    researchTitle: '',
    researchQuery: '',
    isResearching: false,
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
                window.location.reload();
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
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Deep Research & Report Engine</h1>
        <p class="text-muted-foreground">
            AI-powered comprehensive research tool that gathers information from multiple sources
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Reports</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_reports']) }}</p>
                    </div>
                    <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Active Research</p>
                        <p class="text-2xl font-bold">{{ $stats['active_research'] }}</p>
                    </div>
                    <i data-lucide="clock" class="w-8 h-8 text-amber-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Sources Analyzed</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['sources_analyzed']) }}</p>
                    </div>
                    <i data-lucide="globe" class="w-8 h-8 text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Success Rate</p>
                        <p class="text-2xl font-bold">{{ $stats['success_rate'] }}%</p>
                    </div>
                    <i data-lucide="trending-up" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- New Research Form -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                    <i data-lucide="brain" class="w-5 h-5"></i>
                    Start New Research
                </h3>
                <p class="text-sm text-muted-foreground">OpenAI-powered deep research engine with real-time web grounding</p>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div>
                    <label for="research-title" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Research Title</label>
                    <input 
                        x-model="researchTitle"
                        type="text" id="research-title" placeholder="e.g., AI Trends in Healthcare 2025" 
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5" />
                </div>
                <div>
                    <label for="research-query" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Research Query</label>
                    <textarea 
                        x-model="researchQuery"
                        id="research-query" placeholder="Describe what you want to research in detail..." rows="6" 
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5"></textarea>
                </div>
                <div class="flex gap-2">
                    <button 
                        @click="startResearch"
                        :disabled="isResearching"
                        class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        <template x-if="!isResearching">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        </template>
                        <template x-if="isResearching">
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>
                        </template>
                        <span x-text="isResearching ? 'OpenAI is Architecting...' : 'Start Deep Research (OpenAI)'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Research -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row items-center justify-between p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Recent Research</h3>
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                    Filter
                </button>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    @forelse($recentResearches as $research)
                    <div class="p-4 border border-border rounded-lg hover:bg-muted/50 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-sm">{{ $research->title }}</h3>
                            @php
                                $statusClasses = [
                                    'completed' => 'bg-green-100 text-green-700',
                                    'researching' => 'bg-amber-100 text-amber-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                    'pending' => 'bg-slate-100 text-slate-700'
                                ];
                                $currentStatusClass = $statusClasses[$research->status] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-wider transition-colors {{ $currentStatusClass }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                                {{ $research->status }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-muted-foreground mb-3">
                            <span class="flex items-center gap-1">
                                <i data-lucide="globe" class="w-3 h-3"></i>
                                {{ $research->sources_count }} sources
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="file-text" class="w-3 h-3"></i>
                                {{ $research->pages_count }} pages
                            </span>
                            <span>{{ $research->created_at->format('Y-m-d') }}</span>
                        </div>
                        
                        <div class="flex gap-2">
                            <a 
                                href="{{ route('research-engine.show', $research) }}"
                                class="flex-1 inline-flex items-center justify-center rounded-lg text-xs font-black uppercase tracking-wider border border-border bg-white hover:bg-muted transition-all h-9 px-3">
                                <i data-lucide="eye" class="w-3.5 h-3.5 mr-2 text-primary"></i>
                                Preview Research
                            </a>
                            <button 
                                @click="if(confirm('Are you sure you want to stop/delete this research?')) { 
                                    fetch('{{ route('research-engine.destroy', $research) }}', {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    }).then(() => window.location.reload());
                                }"
                                class="inline-flex items-center justify-center rounded-lg text-sm font-medium border border-border bg-white text-destructive hover:bg-destructive/10 h-9 w-9 transition-colors"
                            >
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-muted-foreground">
                        <i data-lucide="search" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                        <p>No research sessions found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
