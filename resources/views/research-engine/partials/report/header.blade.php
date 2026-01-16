{{-- Research Report - Header Section --}}
<div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
    <div>
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('research-engine.index') }}" class="w-10 h-10 rounded-xl bg-muted/50 border border-border flex items-center justify-center hover:bg-primary/10 hover:border-primary/30 transition-all group">
                <i data-lucide="arrow-left" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
            </a>
            <div class="flex flex-col">
                <span class="mono text-[10px] font-black uppercase tracking-[0.3em] text-primary">Protocol: Research Result</span>
                <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground">{{ $research->title }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-6 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Grounding Verified</span>
            </div>
            <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Session ID: {{ substr($research->id, 0, 13) }}...</span>
            <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">{{ $research->created_at->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export MD
        </button>
        <a href="{{ route('document-builder.index', ['research_id' => $research->id]) }}" 
           class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
            Architect Full Report
        </a>
    </div>
</div>
