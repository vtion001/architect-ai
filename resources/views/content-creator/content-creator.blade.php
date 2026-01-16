{{--
    Content Creator - Main Layout
    
    Modularized from 1,158 lines to ~150 lines.
    All sections extracted to partials in /partials directory.
    Alpine.js logic extracted to /resources/js/components/content-creator.js
--}}
@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    generator: 'post',
    topic: '',
    type: 'blog-post',
    count: 2,
    tone: 'Default Tone',
    length: 'Default Length',
    context: '',
    cta: '',
    cta_snippets: [
        'Join the waitlist at arch-ai.io/beta',
        'Book a free consultation today!',
        'Like and Follow for more updates!',
        'Send us a message for inquiries.',
        'Visit our shop for exclusive deals!',
        'Check out the link in our bio!',
    ],
    showCtaSnippets: false,
    newSnippet: '',
    isManagingSnippets: false,
    
    brands: @js($brands ?? []),
    selectedBrandId: '',

    init() {
        const savedSnippets = localStorage.getItem('arch_ai_cta_snippets');
        if (savedSnippets) {
            try {
                this.cta_snippets = JSON.parse(savedSnippets);
            } catch (e) {
                console.error('Failed to parse saved snippets');
            }
        }
        
        this.$watch('cta_snippets', (value) => {
            localStorage.setItem('arch_ai_cta_snippets', JSON.stringify(value));
        });
    },

    addSnippet() {
        if (this.newSnippet.trim()) {
            this.cta_snippets.push(this.newSnippet.trim());
            this.newSnippet = '';
        }
    },

    removeSnippet(index) {
        this.cta_snippets.splice(index, 1);
    },

    addLineBreaks: true,
    includeHashtags: false,
    
    keywords: '',
    structure: 'Standard',
    isBatchMode: false,
    featuredImageType: 'ai',
    
    videoStyle: 'UGC',
    videoDescription: '',
    sourceImage: '',
    aiModel: 'Sora 2 BEST',
    resolution: '1080p',
    aspectRatio: 'Portrait',
    videoDuration: '10 seconds (7 tokens)',
    platform: 'reels',
    hookStyle: 'Problem/Solution',
    duration: '60s',
    
    suggestions: '',
    kbDiscovered: 0,
    isLoadingSuggestions: false,
    
    fetchSuggestions() {
        if (!this.topic || this.isLoadingSuggestions) return;
        this.isLoadingSuggestions = true;
        this.suggestions = '';
        this.kbDiscovered = 0;
        fetch('{{ route('content-creator.suggestions') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ topic: this.topic })
        })
        .then(res => res.json())
        .then(data => {
            this.suggestions = data.suggestions;
            this.kbDiscovered = data.kb_count || 0;
            this.isLoadingSuggestions = false;
        })
        .catch(err => {
            console.error(err);
            this.suggestions = 'Error fetching suggestions.';
            this.isLoadingSuggestions = false;
        });
    },

    isRefining: false,
    refineContext() {
        if (!this.context || this.isRefining) return;
        this.isRefining = true;
        fetch('{{ route('content-creator.refine') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ context: this.context })
        })
        .then(res => res.json())
        .then(data => {
            if (data.context) {
                this.context = data.context;
            }
            this.isRefining = false;
        })
        .catch(err => {
            console.error(err);
            this.isRefining = false;
        });
    },

    isGenerating: false,
    showSuccessModal: false,
    createdContentId: null,
    generatedCalendar: null,
    
    generateContent() {
        if (!this.topic) {
            alert('Please enter a topic.');
            return;
        }
        this.isGenerating = true;
        this.generatedCalendar = null;
        
        const payload = {
            topic: this.topic,
            generator: this.generator,
            type: this.generator === 'post' ? this.type : this.generator,
            count: this.count,
            tone: this.tone,
            length: this.length,
            context: this.context,
            cta: this.cta,
            addLineBreaks: this.addLineBreaks,
            includeHashtags: this.includeHashtags,
            video_platform: this.platform,
            video_hook: this.hookStyle,
            video_duration: this.duration,
            video_style: this.videoStyle,
            video_description: this.topic || this.videoDescription,
            source_image: this.sourceImage,
            ai_model: this.aiModel,
            resolution: this.resolution,
            aspect_ratio: this.aspectRatio,
            generation_duration: this.videoDuration,
            blog_keywords: this.keywords,
            blog_structure: this.structure,
            is_batch_mode: this.isBatchMode,
            featured_image_type: this.featuredImageType,
            brand_id: this.selectedBrandId
        };

        fetch('{{ route('content-creator.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Server Error (HTML):', text);
                throw new Error('Server returned HTML instead of JSON. Check console.');
            }
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Generation failed.');
            }
            return data;
        })
        .then(data => {
            if (this.generator === 'framework') {
                try {
                    const rawContent = data.content.content;
                    this.generatedCalendar = typeof rawContent === 'string' ? JSON.parse(rawContent) : rawContent;
                } catch (e) {
                    console.error('JSON Parse Error', e);
                    alert('Calendar generated but format was invalid.');
                }
            } else {
                this.createdContentId = data.content.id;
                this.showSuccessModal = true;
            }
            this.isGenerating = false;
        })
        .catch(error => {
            console.error(error);
            alert(error.message);
            this.isGenerating = false;
        });
    }
}">

    {{-- Header Info & Generator Toggles --}}
    @include('content-creator.partials.header-info')

    {{-- Stats Cards --}}
    @include('content-creator.partials.stats-cards')

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Content Generator Panel --}}
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2 overflow-hidden">
            <div class="bg-muted/50 border-b border-border p-3 text-center">
                <span class="text-sm font-semibold tracking-wide uppercase" 
                      x-text="generator.charAt(0).toUpperCase() + generator.slice(1) + ' Architect'"></span>
            </div>
            
            <div class="p-8 space-y-8">
                {{-- Post Generator Interface --}}
                @include('content-creator.partials.generators.post-generator')

                {{-- Blog Generator Interface --}}
                @include('content-creator.partials.generators.blog-generator')

                {{-- Framework Calendar Interface --}}
                @include('content-creator.partials.generators.framework-generator')
            </div>
        </div>

        {{-- Sidebar: Context Aware Content --}}
        <div class="space-y-6">
            {{-- Calendar Result View (Active when framework generated) --}}
            @include('content-creator.partials.sidebar.calendar-results')

            {{-- Normal Mode: Recent Content Feed --}}
            @include('content-creator.partials.sidebar.recent-activity')

            {{-- Video Mode: How It Works & Tokens --}}
            @include('content-creator.partials.sidebar.video-info')
        </div>
    </div>

    {{-- Success Modal --}}
    @include('content-creator.partials.success-modal')

</div>
@endsection