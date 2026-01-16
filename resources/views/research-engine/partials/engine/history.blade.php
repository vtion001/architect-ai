{{-- Research Engine - Protocol History --}}
<div class="lg:col-span-3 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Protocol History</h3>
    <div class="grid grid-cols-1 gap-4">
        @forelse($recentResearches as $research)
            @include('research-engine.partials.engine.history-card', ['research' => $research])
        @empty
            <div class="text-center py-20 bg-muted/5 rounded-[40px] border-2 border-dashed border-border opacity-50">
                <i data-lucide="search" class="w-12 h-12 mx-auto mb-4 text-slate-300"></i>
                <p class="text-sm font-medium italic">No active research protocols found.</p>
            </div>
        @endforelse
    </div>
</div>
