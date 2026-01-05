@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Document Archive Protocol</h1>
            <p class="text-muted-foreground font-medium italic">Immutable repository of generated intelligence, reports, and campaign briefs.</p>
        </div>
        <div class="flex gap-3">
            <button class="h-12 px-6 rounded-xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-2 hover:bg-muted transition-all">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Filter Grid
            </button>
        </div>
    </div>

    <!-- Archive Telemetry -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                <i data-lucide="folder" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Archived Assets</p>
                <p class="text-2xl font-black text-white">{{ $stats['total_assets'] }}</p>
            </div>
        </div>
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
            <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500">
                <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Reports Generated</p>
                <p class="text-2xl font-black text-white">{{ $stats['report_count'] }}</p>
            </div>
        </div>
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm group hover:border-primary/30 transition-all">
            <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500">
                <i data-lucide="hard-drive" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Registry Depth</p>
                <p class="text-2xl font-black text-white">{{ $stats['storage_used'] }}</p>
            </div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($documents as $doc)
            <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
                <!-- Meta Badge -->
                <div class="flex items-center justify-between mb-8">
                    <span class="px-2.5 py-1 rounded-lg bg-muted border border-border text-[9px] font-black uppercase tracking-widest text-slate-500">
                        {{ $doc->category ?? 'Intelligence' }}
                    </span>
                    <span class="text-[8px] font-mono text-slate-500 uppercase tracking-tighter">ID: {{ substr($doc->id, 0, 8) }}</span>
                </div>

                <!-- Asset Identity -->
                <div class="space-y-4 mb-8 flex-1">
                    <div class="w-14 h-14 rounded-2xl bg-primary/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-black transition-all">
                        <i data-lucide="{{ $doc->category === 'Reports' ? 'file-spreadsheet' : 'file-text' }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-foreground truncate uppercase tracking-tight group-hover:text-primary transition-colors">{{ $doc->name }}</h3>
                        <p class="text-[10px] text-muted-foreground font-mono mt-1 uppercase">{{ $doc->type }} Asset • {{ round($doc->size / 1024, 1) }} KB</p>
                    </div>
                </div>

                <!-- Footer Stats -->
                <div class="flex items-center justify-between pt-6 border-t border-border/50 mb-8">
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $doc->created_at->diffForHumans() }}</span>
                    </div>
                    @if(isset($doc->metadata['template']))
                        <span class="text-[9px] font-black text-primary uppercase tracking-widest">{{ $doc->metadata['template'] }}</span>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('documents.show', $doc) }}" 
                       class="flex-1 h-12 rounded-xl bg-muted border border-border font-black uppercase text-[10px] tracking-widest flex items-center justify-center hover:bg-white hover:text-black transition-all">
                        Open Viewer
                    </a>
                    <button @click="if(confirm('Purge this intelligence record?')) { fetch('{{ route('documents.destroy', $doc) }}', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => window.location.reload()); }"
                            class="w-12 h-12 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <i data-lucide="archive" class="w-16 h-16 mx-auto text-slate-300"></i>
                <p class="text-sm font-medium">Archive protocol initialized but no assets found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
