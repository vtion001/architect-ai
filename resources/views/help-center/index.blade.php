@extends('layouts.app')

@section('title', 'Help & Center')

@section('content')
<div class="min-h-screen bg-background">
    {{-- Hero Section --}}
    <div class="bg-card border-b border-border shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary/10 backdrop-blur-sm mb-6">
                    <i data-lucide="help-circle" class="w-10 h-10 text-primary"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tighter text-foreground mb-4">
                    Help & Support Center
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Everything you need to master ArchitGrid and boost your productivity
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="mt-10 max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Search for help articles, guides, and tutorials..."
                        class="w-full px-6 py-4 pr-12 rounded-xl border border-border bg-card text-foreground placeholder-muted-foreground shadow-lg focus:ring-2 focus:ring-primary/50 focus:outline-none"
                        x-data="{ query: '' }"
                        x-model="query"
                        @input="searchArticles($event.target.value)"
                    >
                    <i data-lucide="search" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Quick Links --}}
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-foreground mb-6">Popular Topics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('help-center.show', ['section' => 'getting-started', 'article' => 'welcome']) }}" 
                   class="p-4 bg-card rounded-lg border border-border shadow-sm hover:shadow-md hover:border-primary/50 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <i data-lucide="rocket" class="w-5 h-5 text-primary"></i>
                        </div>
                        <span class="font-medium text-foreground group-hover:text-primary transition-colors">Getting Started</span>
                    </div>
                </a>

                <a href="{{ route('help-center.show', ['section' => 'tasks-notes', 'article' => 'creating-tasks']) }}" 
                   class="p-4 bg-card rounded-lg border border-border shadow-sm hover:shadow-md hover:border-primary/50 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <i data-lucide="check-square" class="w-5 h-5 text-primary"></i>
                        </div>
                        <span class="font-medium text-foreground group-hover:text-primary transition-colors">Tasks & Notes</span>
                    </div>
                </a>

                <a href="{{ route('help-center.show', ['section' => 'content-creator', 'article' => 'generating-content']) }}" 
                   class="p-4 bg-card rounded-lg border border-border shadow-sm hover:shadow-md hover:border-primary/50 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <i data-lucide="pencil" class="w-5 h-5 text-primary"></i>
                        </div>
                        <span class="font-medium text-foreground group-hover:text-primary transition-colors">Content Creation</span>
                    </div>
                </a>

                <a href="{{ route('help-center.show', ['section' => 'ai-agents', 'article' => 'creating-agents']) }}" 
                   class="p-4 bg-card rounded-lg border border-border shadow-sm hover:shadow-md hover:border-primary/50 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <i data-lucide="bot" class="w-5 h-5 text-primary"></i>
                        </div>
                        <span class="font-medium text-foreground group-hover:text-primary transition-colors">AI Agents</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- All Sections --}}
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-foreground mb-6">Browse by Category</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($sections as $section)
                <div class="bg-card rounded-xl border border-border shadow-sm hover:shadow-lg hover:border-primary/50 transition-all duration-300 overflow-hidden group">
                    <div class="p-6">
                        {{-- Icon & Title --}}
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/20 group-hover:scale-110 transition-all">
                                <i data-lucide="{{ $section['icon'] }}" class="w-6 h-6 text-primary"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-foreground mb-1 group-hover:text-primary transition-colors">
                                    {{ $section['title'] }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    {{ $section['description'] }}
                                </p>
                            </div>
                        </div>

                        {{-- Articles List --}}
                        <div class="space-y-2">
                            @foreach($section['articles'] as $article)
                            <a href="{{ route('help-center.show', ['section' => $section['slug'], 'article' => $article['slug']]) }}" 
                               class="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary hover:translate-x-1 transition-all">
                                <i data-lucide="arrow-right" class="w-3 h-3 flex-shrink-0"></i>
                                {{ $article['title'] }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- View All Link --}}
                    <div class="px-6 py-3 bg-muted/50 border-t border-border">
                        <a href="{{ route('help-center.show', ['section' => $section['slug'], 'article' => $section['articles'][0]['slug']]) }}" 
                           class="text-sm font-medium text-primary hover:text-primary/80 inline-flex items-center gap-1">
                            Explore {{ $section['title'] }}
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Contact Support --}}
        <div class="bg-card border border-border rounded-2xl p-8 text-center shadow-lg">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 backdrop-blur-sm mb-4">
                <i data-lucide="message-circle" class="w-8 h-8 text-primary"></i>
            </div>
            <h3 class="text-2xl font-bold text-foreground mb-2">Still need help?</h3>
            <p class="text-muted-foreground mb-6 max-w-2xl mx-auto">
                Can't find what you're looking for? Our support team is here to assist you.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="mailto:support@archit-ai.io" class="px-6 py-3 bg-primary text-primary-foreground font-bold uppercase text-xs tracking-widest rounded-lg hover:bg-primary/90 transition-colors inline-flex items-center gap-2 shadow-lg shadow-primary/20">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    Email Support
                </a>
                <a href="#" class="px-6 py-3 bg-muted text-foreground font-medium rounded-lg hover:bg-muted/80 transition-colors inline-flex items-center gap-2">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    Documentation
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function searchArticles(query) {
        // This is a simple client-side search implementation
        // In production, you might want to implement server-side search
        console.log('Searching for:', query);
        // You can implement filtering logic here
    }
</script>
@endsection
