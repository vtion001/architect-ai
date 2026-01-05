@extends('layouts.app')

@section('content')
<div class="p-10 max-w-[1200px] mx-auto animate-in fade-in duration-700">
    <!-- Archive Header -->
    <div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('documents.index') }}" class="w-10 h-10 rounded-xl bg-muted/50 border border-border flex items-center justify-center hover:bg-primary/10 hover:border-primary/30 transition-all group">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
                </a>
                <div class="flex flex-col">
                    <span class="mono text-[10px] font-black uppercase tracking-[0.3em] text-primary">Protocol: Archived Intelligence</span>
                    <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground">{{ $document->name }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-6 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">{{ $document->category ?? 'General' }} Archive</span>
                </div>
                <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Asset UUID: {{ substr($document->id, 0, 13) }}...</span>
                <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Stored: {{ $document->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export Archive
            </button>
            <button class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                <i data-lucide="share-2" class="w-4 h-4"></i>
                Synchronize Context
            </button>
        </div>
    </div>

    <!-- Document Content Node -->
    <div class="bg-white border border-border rounded-[40px] shadow-2xl relative overflow-hidden">
        <!-- Decorative Blueprint Header -->
        <div class="h-2 bg-gradient-to-r from-blue-400/40 via-blue-500 to-blue-400/40"></div>
        
        <div class="p-16 md:p-24">
            <div class="prose-architect max-w-none">
                {!! $document->content !!}
            </div>
        </div>

        <!-- Footer Watermark -->
        <div class="p-10 border-t border-border bg-muted/10 flex justify-between items-center opacity-30 mono text-[8px] font-black uppercase tracking-[0.4em]">
            <span>ArchitGrid Registry v1.0.4</span>
            <span>Integrity Verified: {{ hash('sha256', $document->content) }}</span>
        </div>
    </div>
</div>

<style>
    /* Architectural Document Styling */
    .prose-architect {
        font-family: 'Inter', sans-serif;
        color: #1e293b;
        line-height: 1.8;
    }
    /* Handle both Markdown-style and HTML content within the viewer */
    .prose-architect h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; color: #0f172a; font-size: 2.25rem; margin-bottom: 2rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem; }
    .prose-architect h2 { font-family: 'Montserrat', sans-serif; font-weight: 800; color: #1e293b; font-size: 1.5rem; margin-top: 3rem; margin-bottom: 1rem; }
    .prose-architect p { margin-bottom: 1.5rem; font-size: 1.05rem; }
    .prose-architect table { width: 100%; border-collapse: collapse; margin: 2rem 0; }
    .prose-architect th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; text-align: left; font-size: 0.8rem; font-weight: 900; text-transform: uppercase; }
    .prose-architect td { border: 1px solid #e2e8f0; padding: 12px; }
</style>
@endsection
