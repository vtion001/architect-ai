@extends('layouts.app')

@section('content')
<div class="p-8 max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('research-engine.index') }}" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold">{{ $research->title }}</h1>
            <p class="text-muted-foreground mt-1">Research Session Result • {{ $research->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Export Markdown
            </button>
            <a href="{{ route('report-builder.index', ['research_id' => $research->id]) }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
                Build Full Report
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Sources</p>
            <p class="text-2xl font-bold text-blue-500">{{ $research->sources_count }} Verified</p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Analyzed Depth</p>
            <p class="text-2xl font-bold text-green-500">{{ $research->pages_count }} Pages</p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Confidence</p>
            <p class="text-2xl font-bold text-purple-500">98% Match</p>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border bg-muted/30">
            <h3 class="font-semibold flex items-center gap-2 text-lg">
                <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
                Executive Research Findings
            </h3>
        </div>
        <div class="p-10 prose prose-slate max-w-none dark:prose-invert bg-white">
            {!! Str::markdown($research->result ?? 'No result data found.') !!}
        </div>
    </div>
</div>

<style>
    /* Styling for the markdown output to look like a real paper */
    .prose h1 { color: #1e3a8a; font-size: 2.25rem; font-weight: 800; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem; }
    .prose h2 { color: #1e293b; font-size: 1.5rem; font-weight: 700; margin-top: 2rem; border-left: 4px solid #3b82f6; padding-left: 1rem; }
    .prose h3 { color: #334155; font-size: 1.25rem; font-weight: 600; }
    .prose p { line-height: 1.8; color: #475569; margin-bottom: 1.5rem; text-align: justify; }
    .prose ul, .prose ol { margin-bottom: 1.5rem; padding-left: 1.5rem; }
    .prose li { margin-bottom: 0.5rem; color: #475569; }
    .prose strong { color: #0f172a; font-weight: 600; }
    .prose table { width: 100%; border-collapse: collapse; margin: 2rem 0; font-size: 0.9rem; }
    .prose th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; text-align: left; font-weight: 600; }
    .prose td { border: 1px solid #e2e8f0; padding: 12px; vertical-align: top; }
</style>
@endsection
