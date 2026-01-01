@extends('layouts.app')

@section('content')
<div class="p-8 max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('content-creator.index') }}" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Creator
            </a>
            <h1 class="text-3xl font-bold">{{ $content->title }}</h1>
            <p class="text-muted-foreground mt-1">{{ ucwords(str_replace('-', ' ', $content->type)) }} • {{ $content->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                <i data-lucide="copy" class="w-4 h-4 mr-2"></i>
                Copy Content
            </button>
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                Publish
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Length</p>
            <p class="text-2xl font-bold text-blue-500">{{ $content->word_count }} Words</p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Topic</p>
            <p class="text-lg font-bold truncate">{{ $content->topic }}</p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Status</p>
            <p class="text-2xl font-bold text-purple-500 uppercase">{{ $content->status }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden min-h-[600px]">
        <div class="p-6 border-b border-border bg-muted/30 flex justify-between items-center">
            <h3 class="font-semibold flex items-center gap-2 text-lg">
                <i data-lucide="align-left" class="w-5 h-5 text-primary"></i>
                Generated Masterpiece
            </h3>
            <span class="text-xs text-muted-foreground italic">Markdown Supported</span>
        </div>
        <div class="p-10 prose prose-slate max-w-none dark:prose-invert bg-white">
            {!! Str::markdown($content->result ?? 'No content generated.') !!}
        </div>
    </div>
</div>
@endsection
