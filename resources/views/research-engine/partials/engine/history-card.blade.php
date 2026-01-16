{{-- Research Engine - History Card --}}
@php
    $statusColors = [
        'completed' => 'text-green-600 bg-green-50 border-green-100',
        'researching' => 'text-amber-600 bg-amber-50 border-amber-100',
        'failed' => 'text-red-600 bg-red-50 border-red-100'
    ];
    $statusColor = $statusColors[$research->status] ?? 'text-slate-600 bg-slate-50 border-slate-100';
@endphp

<div class="p-6 bg-card border border-border rounded-[32px] hover:border-primary/30 transition-all group">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="font-black text-lg text-foreground uppercase tracking-tight">{{ $research->title }}</h3>
            <p class="text-[10px] text-muted-foreground font-mono mt-1">{{ $research->created_at->diffForHumans() }}</p>
        </div>
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
