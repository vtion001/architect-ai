{{-- Documents Index - Document Card --}}
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
        <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-border/50 overflow-hidden relative flex items-center justify-center group-hover:border-primary transition-all shadow-inner">
            <img src="/images/templates/{{ $doc->metadata['template'] ?? 'custom' }}.png" 
                 class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-all duration-500" 
                 onerror="this.src='/images/templates/custom.png'">
            <i data-lucide="{{ $doc->category === 'Reports' ? 'file-spreadsheet' : 'file-text' }}" class="relative z-10 w-5 h-5 text-white/40 group-hover:scale-110 transition-transform"></i>
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
