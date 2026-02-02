@extends('layouts.app')

@section('title', $article['title'] . ' - Help Center')

@section('content')
<div class="min-h-screen bg-background">
    {{-- Breadcrumb Header --}}
    <div class="bg-card border-b border-border shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <a href="{{ route('help-center.index') }}" class="hover:text-primary transition-colors inline-flex items-center gap-1">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    Help Center
                </a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-foreground font-medium">{{ $section['title'] }}</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-foreground font-medium">{{ $article['title'] }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar Navigation --}}
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-foreground uppercase tracking-wide mb-4">
                            In This Section
                        </h3>
                        <nav class="space-y-1">
                            @foreach($section['articles'] as $sectionArticle)
                            <a href="{{ route('help-center.show', ['section' => $section['slug'], 'article' => $sectionArticle['slug']]) }}" 
                               class="block px-3 py-2 rounded-lg text-sm transition-colors {{ $sectionArticle['slug'] === $article['slug'] ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-muted/50 hover:text-foreground' }}">
                                {{ $sectionArticle['title'] }}
                            </a>
                            @endforeach
                        </nav>

                        {{-- Other Sections --}}
                        <div class="mt-8 pt-6 border-t border-border">
                            <h3 class="text-sm font-semibold text-foreground uppercase tracking-wide mb-4">
                                Other Categories
                            </h3>
                            <nav class="space-y-1">
                                @foreach($allSections as $otherSection)
                                    @if($otherSection['slug'] !== $section['slug'])
                                    <a href="{{ route('help-center.show', ['section' => $otherSection['slug'], 'article' => $otherSection['articles'][0]['slug']]) }}" 
                                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:bg-muted/50 hover:text-foreground transition-colors">
                                        <i data-lucide="{{ $otherSection['icon'] }}" class="w-4 h-4 flex-shrink-0"></i>
                                        <span class="truncate">{{ $otherSection['title'] }}</span>
                                    </a>
                                    @endif
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-3">
                <article class="bg-card border border-border rounded-xl shadow-sm">
                    {{-- Article Header --}}
                    <div class="px-8 py-6 border-b border-border">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i data-lucide="{{ $section['icon'] }}" class="w-6 h-6 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">{{ $section['title'] }}</p>
                                <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground">{{ $article['title'] }}</h1>
                            </div>
                        </div>
                    </div>

                    {{-- Article Content --}}
                    <div class="px-8 py-8">
                        <div class="prose prose-cyan max-w-none">
                            {!! Str::markdown($article['content']) !!}
                        </div>
                    </div>

                    {{-- Article Footer --}}
                    <div class="px-8 py-6 bg-muted/50 border-t border-border rounded-b-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground mb-2">Was this article helpful?</p>
                                <div class="flex gap-2">
                                    <button class="px-4 py-2 bg-card border border-border rounded-lg text-sm font-medium text-foreground hover:bg-muted/50 transition-colors inline-flex items-center gap-2">
                                        <i data-lucide="thumbs-up" class="w-4 h-4"></i>
                                        Yes
                                    </button>
                                    <button class="px-4 py-2 bg-card border border-border rounded-lg text-sm font-medium text-foreground hover:bg-muted/50 transition-colors inline-flex items-center gap-2">
                                        <i data-lucide="thumbs-down" class="w-4 h-4"></i>
                                        No
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-muted-foreground mb-2">Need more help?</p>
                                <a href="mailto:support@archit-ai.io" class="text-sm font-medium text-primary hover:text-primary/80 inline-flex items-center gap-1">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                    Contact Support
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

                {{-- Related Articles --}}
                @if(count($section['articles']) > 1)
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-foreground mb-4">Related Articles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($section['articles'] as $relatedArticle)
                            @if($relatedArticle['slug'] !== $article['slug'])
                            <a href="{{ route('help-center.show', ['section' => $section['slug'], 'article' => $relatedArticle['slug']]) }}" 
                               class="p-6 bg-card border border-border rounded-xl shadow-sm hover:shadow-md hover:border-primary/50 transition-all group">
                                <h3 class="font-semibold text-foreground mb-2 group-hover:text-primary transition-colors">
                                    {{ $relatedArticle['title'] }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Learn more about {{ strtolower($relatedArticle['title']) }}
                                </p>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Enhanced Markdown Styles - ArchitGrid Theme */
    .prose {
        color: var(--foreground);
        line-height: 1.75;
    }
    
    .prose h1 {
        font-size: 2.25em;
        margin-top: 0;
        margin-bottom: 0.8888889em;
        line-height: 1.1111111;
        font-weight: 900;
        letter-spacing: -0.025em;
        text-transform: uppercase;
        color: var(--foreground);
    }
    
    .prose h2 {
        font-size: 1.5em;
        margin-top: 2em;
        margin-bottom: 1em;
        line-height: 1.3333333;
        font-weight: 700;
        color: var(--foreground);
    }
    
    .prose h3 {
        font-size: 1.25em;
        margin-top: 1.6em;
        margin-bottom: 0.6em;
        line-height: 1.6;
        font-weight: 600;
        color: var(--foreground);
    }
    
    .prose h4 {
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        line-height: 1.5;
        font-weight: 600;
        color: var(--foreground);
    }
    
    .prose p {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
    }
    
    .prose a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    
    .prose a:hover {
        color: var(--primary);
        opacity: 0.8;
        text-decoration: underline;
    }
    
    .prose strong {
        color: var(--foreground);
        font-weight: 700;
    }
    
    .prose ul {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        list-style-type: disc;
        padding-left: 1.625em;
    }
    
    .prose ol {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        list-style-type: decimal;
        padding-left: 1.625em;
    }
    
    .prose li {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    
    .prose code {
        color: var(--foreground);
        font-weight: 600;
        font-size: 0.875em;
        background-color: var(--muted);
        padding: 0.25em 0.5em;
        border-radius: 0.375rem;
    }
    
    .prose pre {
        background-color: var(--sidebar);
        color: var(--muted-foreground);
        overflow-x: auto;
        font-size: 0.875em;
        line-height: 1.7142857;
        margin-top: 1.7142857em;
        margin-bottom: 1.7142857em;
        border-radius: 0.5rem;
        padding: 1em 1.5em;
        border: 1px solid var(--border);
    }
    
    .prose pre code {
        background-color: transparent;
        border-width: 0;
        border-radius: 0;
        padding: 0;
        font-weight: 400;
        color: inherit;
        font-size: inherit;
        font-family: inherit;
        line-height: inherit;
    }
    
    .prose blockquote {
        font-weight: 500;
        font-style: italic;
        color: var(--foreground);
        border-left-width: 0.25rem;
        border-left-color: var(--primary);
        quotes: '\201C' '\201D' '\2018' '\2019';
        margin-top: 1.6em;
        margin-bottom: 1.6em;
        padding-left: 1em;
        background-color: var(--muted);
        padding: 1em;
        border-radius: 0.5rem;
    }
    
    .prose hr {
        border-color: var(--border);
        border-top-width: 1px;
        margin-top: 3em;
        margin-bottom: 3em;
    }
</style>
@endpush
@endsection

        line-height: 1.75;
    }
    
    .prose h1 {
        font-size: 2.25em;
        margin-top: 0;
        margin-bottom: 0.8888889em;
        line-height: 1.1111111;
        font-weight: 800;
        color: #111827;
    }
    
    .prose h2 {
        font-size: 1.5em;
        margin-top: 2em;
        margin-bottom: 1em;
        line-height: 1.3333333;
        font-weight: 700;
        color: #111827;
    }
    
    .prose h3 {
        font-size: 1.25em;
        margin-top: 1.6em;
        margin-bottom: 0.6em;
        line-height: 1.6;
        font-weight: 600;
        color: #111827;
    }
    
    .prose h4 {
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        line-height: 1.5;
        font-weight: 600;
        color: #111827;
    }
    
    .prose p {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
    }
    
    .prose a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
    }
    
    .prose a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }
    
    .prose strong {
        color: #111827;
        font-weight: 600;
    }
    
    .prose ul {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        list-style-type: disc;
        padding-left: 1.625em;
    }
    
    .prose ol {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        list-style-type: decimal;
        padding-left: 1.625em;
    }
    
    .prose li {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    
    .prose code {
        color: #111827;
        font-weight: 600;
        font-size: 0.875em;
        background-color: #f3f4f6;
        padding: 0.25em 0.5em;
        border-radius: 0.375rem;
    }
    
    .prose pre {
        background-color: #1f2937;
        color: #f9fafb;
        overflow-x: auto;
        font-size: 0.875em;
        line-height: 1.7142857;
        margin-top: 1.7142857em;
        margin-bottom: 1.7142857em;
        border-radius: 0.5rem;
        padding: 1em 1.5em;
    }
    
    .prose pre code {
        background-color: transparent;
        border-width: 0;
        border-radius: 0;
        padding: 0;
        font-weight: 400;
        color: inherit;
        font-size: inherit;
        font-family: inherit;
        line-height: inherit;
    }
    
    .prose blockquote {
        font-weight: 500;
        font-style: italic;
        color: #111827;
        border-left-width: 0.25rem;
        border-left-color: #e5e7eb;
        quotes: '\\201C' '\\201D' '\\2018' '\\2019';
        margin-top: 1.6em;
        margin-bottom: 1.6em;
        padding-left: 1em;
    }
    
    .prose hr {
        border-color: #e5e7eb;
        border-top-width: 1px;
        margin-top: 3em;
        margin-bottom: 3em;
    }
</style>
@endpush
@endsection
