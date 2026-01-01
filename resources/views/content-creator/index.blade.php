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
    addLineBreaks: true,
    includeHashtags: false,
    
    // Blog Specific
    keywords: '',
    structure: 'Standard',
    
    // Video Specific
    platform: 'reels',
    hookStyle: 'Problem/Solution',
    duration: '60s',
    
    isGenerating: false,
    generateContent() {
        if (!this.topic) {
            alert('Please enter a topic.');
            return;
        }
        this.isGenerating = true;
        
        // Bundle parameters based on generator type
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
            
            // Mode-specific options
            video_platform: this.platform,
            video_hook: this.hookStyle,
            video_duration: this.duration,
            blog_keywords: this.keywords,
            blog_structure: this.structure
        };

        fetch('{{ route('content-creator.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Generation failed: ' + (data.message || 'Unknown error'));
                this.isGenerating = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isGenerating = false;
        });
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Knowledge-Base Driven Content Creator</h1>
        <p class="text-muted-foreground">Generate high-quality content powered by your knowledge base</p>
    </div>

    <div class="mb-8 rounded-xl border border-primary/20 bg-primary/5 p-6 border-l-4 border-l-primary flex flex-col md:flex-row justify-between gap-6">
        <div class="flex gap-4 flex-1">
            <div class="rounded-full bg-primary/10 p-2 h-fit">
                <i data-lucide="help-circle" class="w-6 h-6 text-primary"></i>
            </div>
            <div>
                <h3 class="font-semibold text-lg mb-2">How to Use This Page</h3>
                <ol class="text-sm text-muted-foreground space-y-2 list-decimal list-inside">
                    <li>Enter your topic and generate posts using the form below</li>
                    <li>Review and edit your generated posts on the right</li>
                    <li>Select posts using checkboxes, then use "Bulk Images" or "Bulk Schedule"</li>
                    <li>For individual posts, click "Post Now" or "Schedule" to choose platforms</li>
                    <li>Platform selection happens when posting/scheduling, not during generation</li>
                </ol>
            </div>
        </div>
        
        <!-- Generator Toggles (Matching User Photo) -->
        <div class="flex flex-col gap-2 min-w-[200px]">
            <button @click="generator = 'video'" :class="generator === 'video' ? 'bg-slate-800 text-white shadow-xl shadow-black/20 font-bold border-white/10 ring-1 ring-white/20' : 'bg-slate-900 text-white/70 hover:text-white border-white/5 font-medium'" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-sm border">
                <i data-lucide="video" class="w-4 h-4"></i>
                Video Generator
            </button>
            <button @click="generator = 'post'" :class="generator === 'post' ? 'bg-slate-800 text-white shadow-xl shadow-black/20 font-bold border-white/10 ring-1 ring-white/20' : 'bg-slate-900 text-white/70 hover:text-white border-white/5 font-medium'" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-sm border">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Post Generator
            </button>
            <button @click="generator = 'blog'" :class="generator === 'blog' ? 'bg-slate-800 text-white shadow-xl shadow-black/20 font-bold border-white/10 ring-1 ring-white/20' : 'bg-slate-900 text-white/70 hover:text-white border-white/5 font-medium'" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-sm border">
                <i data-lucide="book" class="w-4 h-4"></i>
                Blog Generator
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Content</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_content']) }}</p>
                    </div>
                    <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                </div>
            </div>
        </div>
        <!-- This Month -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">This Month</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['this_month']) }}</p>
                    </div>
                    <i data-lucide="trending-up" class="w-8 h-8 text-green-500"></i>
                </div>
            </div>
        </div>
        <!-- In Draft -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">In Draft</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['in_draft']) }}</p>
                    </div>
                    <i data-lucide="pencil" class="w-8 h-8 text-amber-500"></i>
                </div>
            </div>
        </div>
        <!-- Published -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Published</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['published']) }}</p>
                    </div>
                    <i data-lucide="sparkles" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content Generator -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2 overflow-hidden">
            <div class="bg-muted/50 border-b border-border p-3 text-center">
                <span class="text-sm font-semibold tracking-wide uppercase" x-text="generator.charAt(0).toUpperCase() + generator.slice(1) + ' Architect'"></span>
            </div>
            
            <div class="p-8 space-y-8">
                <!-- Post Generator Interface -->
                <div x-show="generator === 'post'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-8">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Architect Single/Bulk Posts</h3>
                        <p class="text-sm text-muted-foreground">Define parameters for high-engagement text posts powered by your knowledge base.</p>
                    </div>

                    <!-- Main Topic -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-bold uppercase tracking-tight">Post Topic / Theme</label>
                            <button class="text-xs text-primary font-medium flex items-center gap-1 hover:underline">
                                <i data-lucide="lightbulb" class="w-3.5 h-3.5"></i>
                                Inspiration
                            </button>
                        </div>
                        <input x-model="topic" type="text" placeholder="e.g., 'Modern Architecture Trends 2026'" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                    </div>

                    <!-- Parameters Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Quantity</label>
                            <input x-model="count" type="number" min="1" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Tone</label>
                            <select x-model="tone" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                                <option>Professional</option>
                                <option>Casual</option>
                                <option>Provocative</option>
                                <option>Empathetic</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Format</label>
                            <select x-model="type" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                                <option value="social-media">Social Media</option>
                                <option value="email">Direct Email</option>
                                <option value="ad-copy">Ad Copy</option>
                            </select>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight">Mandate / Specific Context</label>
                        <textarea x-model="context" placeholder="e.g., 'Focus on sustainable materials and eco-friendly designs...'" rows="4" class="flex min-h-[100px] w-full rounded-lg border border-input bg-muted/30 px-4 py-3 text-sm"></textarea>
                    </div>
                </div>

                <!-- Video Generator Interface -->
                <div x-show="generator === 'video'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-8" style="display: none;">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Architect Video Scripts</h3>
                        <p class="text-sm text-muted-foreground">Generate viral-ready scripts with visual hooks and storyboards.</p>
                    </div>

                    <div class="space-y-4">
                        <label class="text-sm font-bold uppercase tracking-tight">Script Topic</label>
                        <input x-model="topic" type="text" placeholder="e.g., 'Behind the scenes of our new studio'" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Platform</label>
                            <select x-model="platform" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                                <option value="tiktok">TikTok</option>
                                <option value="reels">Instagram Reels</option>
                                <option value="youtube">YouTube Shorts</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Hook Style</label>
                            <select x-model="hookStyle" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                                <option>Problem/Solution</option>
                                <option>Curiosity Gap</option>
                                <option>Direct Fact</option>
                                <option>Question Hook</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Target Length</label>
                            <select x-model="duration" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                                <option>15s</option>
                                <option>30s</option>
                                <option>60s</option>
                                <option>90s</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight">Key Visual Elements to Include</label>
                        <textarea x-model="context" placeholder="Add specific visual cues or props..." rows="4" class="flex min-h-[100px] w-full rounded-lg border border-input bg-muted/30 px-4 py-3 text-sm"></textarea>
                    </div>
                </div>

                <!-- Blog Generator Interface -->
                <div x-show="generator === 'blog'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-8" style="display: none;">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Architect Long-Form Blogs</h3>
                        <p class="text-sm text-muted-foreground">Generate SEO-optimized articles with technical depth and structure.</p>
                    </div>

                    <div class="space-y-4">
                        <label class="text-sm font-bold uppercase tracking-tight">Article Title / Main Theme</label>
                        <input x-model="topic" type="text" placeholder="e.g., 'The Future of Agentic Coding Foundations'" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Target Keyword</label>
                            <input x-model="keywords" type="text" placeholder="e.g., 'sustainable architecture'" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight">Article Structure</label>
                            <select x-model="structure" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                                <option>Standard Article</option>
                                <option>Detailed Case Study</option>
                                <option>How-To Guide</option>
                                <option>Comparison Analysis</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight">Outline Mandate</label>
                        <textarea x-model="context" placeholder="e.g., 'Include a section on carbon footprint and one on recycled steel...'" rows="4" class="flex min-h-[100px] w-full rounded-lg border border-input bg-muted/30 px-4 py-3 text-sm"></textarea>
                    </div>
                </div>

                <!-- Shared CTA & Checkboxes -->
                <div class="space-y-6 pt-6 border-t border-border/50">
                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight text-primary/80">Global Call to Action</label>
                        <input x-model="cta" type="text" placeholder="e.g., 'Join the waitlist at arch-ai.io/beta'" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                            <input type="checkbox" x-model="addLineBreaks" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-medium leading-none">Generous Spacing</span>
                                <i data-lucide="help-circle" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors" x-show="generator !== 'blog'">
                            <input type="checkbox" x-model="includeHashtags" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-medium leading-none">Include Hashtags</span>
                                <i data-lucide="hash" class="w-3.5 h-3.5 text-muted-foreground"></i>
                            </div>
                        </label>
                    </div>

                    <div class="text-xs text-muted-foreground mt-4 italic">
                        Estimated Token Consumption: <span class="font-bold text-foreground" x-text="generator === 'post' ? count : (generator === 'blog' ? 5 : 3)"></span>
                    </div>

                    <button @click="generateContent" :disabled="isGenerating" class="w-full inline-flex items-center justify-center rounded-xl text-md font-bold ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:scale-[1.01] active:scale-[0.99] h-14 px-8 py-4 shadow-lg shadow-primary/20">
                        <template x-if="!isGenerating">
                            <div class="flex items-center gap-2">
                                <i data-lucide="sparkles" class="w-5 h-5"></i>
                                <span>Architect <span x-text="generator.toUpperCase()"></span> content</span>
                            </div>
                        </template>
                        <template x-if="isGenerating">
                            <div class="flex items-center gap-2">
                                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                <span>Architecting...</span>
                            </div>
                        </template>
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Recent Content</h3>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    @forelse($recentContents as $item)
                    <a href="{{ route('content-creator.show', $item) }}" class="block p-3 border border-border rounded-lg hover:bg-muted/50">
                        <h3 class="font-semibold text-sm mb-2">{{ $item->title }}</h3>
                        <div class="flex items-center justify-between text-xs text-muted-foreground mb-2">
                            <span>{{ ucwords(str_replace('-', ' ', $item->type)) }}</span>
                            <span>{{ $item->word_count }} words</span>
                        </div>
                        @php
                            $statusClasses = [
                                'published' => 'bg-green-100 text-green-700',
                                'draft' => 'bg-amber-100 text-amber-700',
                                'generating' => 'bg-blue-100 text-blue-700',
                                'failed' => 'bg-red-100 text-red-700'
                            ];
                            $currentStatusClass = $statusClasses[$item->status] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 {{ $currentStatusClass }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </a>
                    @empty
                    <div class="text-center py-8 text-muted-foreground">
                        <i data-lucide="pencil" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                        <p>No content generated yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
