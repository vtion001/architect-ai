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
    isBatchMode: false,
    featuredImageType: 'ai',
    
    // Video Specific
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
    isLoadingSuggestions: false,
    fetchSuggestions() {
        if (!this.topic || this.isLoadingSuggestions) return;
        this.isLoadingSuggestions = true;
        this.suggestions = '';
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
            
            // New Video Params
            video_style: this.videoStyle,
            video_description: this.topic || this.videoDescription,
            source_image: this.sourceImage,
            ai_model: this.aiModel,
            resolution: this.resolution,
            aspect_ratio: this.aspectRatio,
            generation_duration: this.videoDuration,

            // Blog Params
            blog_keywords: this.keywords,
            blog_structure: this.structure,
            is_batch_mode: this.isBatchMode,
            featured_image_type: this.featuredImageType,
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
                    <!-- Post Header -->
                    <div class="mb-2">
                        <div class="flex items-center gap-3 mb-1">
                            <i data-lucide="edit-3" class="w-6 h-6 text-primary"></i>
                            <h2 class="text-2xl font-black text-foreground">Post Architect</h2>
                        </div>
                        <p class="text-sm text-muted-foreground font-medium">Define parameters for high-engagement text posts powered by your knowledge base.</p>
                    </div>

                    <!-- Main Topic -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Post Topic / Theme <span class="text-red-500">*</span></label>
                            <button @click="fetchSuggestions()" :disabled="isLoadingSuggestions || !topic" class="bg-muted px-3 py-1 rounded border border-border text-[10px] font-bold flex items-center gap-1.5 hover:bg-muted/80 disabled:opacity-50">
                                <span x-show="!isLoadingSuggestions" class="flex items-center gap-1.5">
                                    <i data-lucide="sparkles" class="w-3 h-3 text-primary"></i>
                                    GET SUGGESTIONS
                                </span>
                                <span x-show="isLoadingSuggestions">Running OpenAI...</span>
                            </button>
                        </div>
                        <input x-model="topic" type="text" placeholder="e.g., 'Modern Architecture Trends 2026'" class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
                        
                        <!-- Suggestions Results -->
                        <div x-show="suggestions" x-transition class="p-4 bg-muted/30 border border-border rounded-lg relative">
                            <button @click="suggestions = ''" class="absolute top-2 right-2 text-muted-foreground hover:text-foreground">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                            <h4 class="text-xs font-bold uppercase mb-2 text-primary">OpenAI Ideas:</h4>
                            <div class="prose prose-sm max-w-none text-muted-foreground whitespace-pre-wrap text-sm" x-text="suggestions"></div>
                        </div>
                    </div>

                    <!-- Parameters Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Quantity</label>
                            <input x-model="count" type="number" min="1" max="100" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Tone</label>
                            <select x-model="tone" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                                <option>Professional</option>
                                <option>Casual</option>
                                <option>Provocative</option>
                                <option>Empathetic</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Format</label>
                            <select x-model="type" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                                <option value="social-media">Social Media</option>
                                <option value="email">Direct Email</option>
                                <option value="ad-copy">Ad Copy</option>
                            </select>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Mandate / Specific Context</label>
                            <button @click="refineContext()" :disabled="!context || isRefining" class="bg-muted px-3 py-1 rounded border border-border text-[10px] font-bold flex items-center gap-1.5 hover:bg-muted/80 disabled:opacity-50">
                                <span x-show="!isRefining" class="flex items-center gap-1.5">
                                    <i data-lucide="wand-2" class="w-3 h-3 text-primary"></i>
                                    AI REWRITE ASSIST
                                </span>
                                <span x-show="isRefining">Polishing...</span>
                            </button>
                        </div>
                        <textarea x-model="context" placeholder="e.g., 'Focus on sustainable materials and eco-friendly designs...'" rows="4" class="w-full min-h-[120px] bg-muted/20 border border-border rounded-xl px-5 py-4 text-sm font-medium focus:ring-1 focus:ring-primary"></textarea>
                    </div>

                    <!-- Shared Parameters (Integrated) -->
                    <div class="space-y-6 pt-6 border-t border-border/50">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-primary/80 italic">Global Call to Action</label>
                            <input x-model="cta" type="text" placeholder="e.g., 'Join the waitlist at arch-ai.io/beta'" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                                <input type="checkbox" x-model="addLineBreaks" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold leading-none uppercase tracking-tight">Generous Spacing</span>
                                    <i data-lucide="help-circle" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                                <input type="checkbox" x-model="includeHashtags" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold leading-none uppercase tracking-tight">Include Hashtags</span>
                                    <i data-lucide="hash" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="text-xs text-muted-foreground mt-4 italic">
                        Estimated Token Consumption: <span class="font-bold text-foreground" x-text="count"></span>
                    </div>

                    <!-- Generate Button Area -->
                    <div class="pt-4">
                        <button @click="generateContent" :disabled="isGenerating" class="w-full h-14 bg-primary hover:opacity-90 text-primary-foreground rounded-xl font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3 text-xs disabled:opacity-50 disabled:pointer-events-none">
                            <template x-if="!isGenerating">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                                    <span>Generate & Preview Posts</span>
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

                    <!-- Features List -->
                    <div class="bg-primary/5 border border-primary/10 rounded-xl p-6 space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="layout" class="w-4 h-4 text-primary"></i>
                            <h4 class="text-[10px] font-black text-primary uppercase tracking-wider">AI Post Architect Features:</h4>
                        </div>
                        <div class="grid grid-cols-1 gap-1.5">
                            <template x-for="feature in [
                                'Multi-platform optimization (X, LinkedIn, Meta)',
                                'Brand voice synchronization from knowledge base',
                                'Automated hook generation for high engagement',
                                'Direct scheduling integration',
                                'Bulk generation capabilities'
                            ]">
                                <div class="flex items-start gap-2.5">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-primary shrink-0 mt-0.5"></i>
                                    <p class="text-[10px] font-semibold text-muted-foreground leading-tight" x-text="feature"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Video Generator Interface -->
                <div x-show="generator === 'video'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-8" style="display: none;">
                    <!-- Secondary Nav Tabs -->
                    <div class="bg-muted/50 rounded-lg p-1 flex items-center justify-center gap-1 mb-8">
                        <button class="px-4 py-1.5 rounded-md bg-white shadow-sm text-xs font-bold text-foreground">Generate New Video</button>
                        <button class="px-4 py-1.5 rounded-md text-xs font-bold text-muted-foreground hover:bg-white/50">Video Queue</button>
                        <button class="px-4 py-1.5 rounded-md text-xs font-bold text-muted-foreground hover:bg-white/50">Completed</button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-1">Video Generation</h3>
                            <p class="text-sm text-muted-foreground font-medium">Enter a prompt and configure your video settings</p>
                        </div>
                        <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1.5 rounded-full border border-primary/20">7 tokens per video</span>
                    </div>

                    <!-- Style Preset -->
                    <div class="space-y-4">
                        <label class="text-sm font-bold uppercase tracking-tight italic">Style Preset <span class="text-muted-foreground ml-1 font-normal opacity-70">(Optional)</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            <template x-for="style in [
                                { name: 'UGC', id: '1516245834210-c4c142787335' },
                                { name: 'Cartoon', id: '1578632738980-42445202d089' },
                                { name: 'Futuristic', id: '1485827404703-89b55fcc595e' },
                                { name: 'Cinematic', id: '1485846234645-a62644f84728' },
                                { name: 'Documentary', id: '1526628953301-3e589a6a8b74' },
                                { name: 'LEGO', id: '1585366119957-e5769bb3f7f3' },
                                { name: 'Vintage', id: '1524230572899-a752b3835840' },
                                { name: 'Abstract', id: '1541701494587-cb58502866ab' },
                                { name: 'Nature', id: '1441974231531-c6227db76b6e' },
                                { name: 'Urban', id: '1449824913935-59a10b8d2000' }
                            ]">
                                <button @click="videoStyle = style.name" :class="videoStyle === style.name ? 'ring-2 ring-primary ring-offset-2 scale-[1.02]' : 'opacity-80 hover:opacity-100'" class="relative group aspect-video rounded-lg overflow-hidden border border-border transition-all">
                                    <img :src="'https://images.unsplash.com/photo-' + style.id + '?q=80&w=300&auto=format&fit=crop'" class="w-full h-full object-cover">
                                    <div class="absolute inset-x-0 bottom-0 bg-black/60 backdrop-blur-[2px] p-2">
                                        <p class="text-[9px] font-black text-white text-center uppercase tracking-[0.1em]" x-text="style.name"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                        <p class="text-[10px] text-muted-foreground font-medium opacity-70">Select a style to automatically enhance your video description</p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-primary/5 border border-primary/10 rounded-lg p-4 flex gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-primary shrink-0"></i>
                        <p class="text-xs text-muted-foreground leading-relaxed">
                            <span class="font-bold text-primary">Add yourselves or friends to your videos!</span> Tag your Sora 2 Cameo or a friend's Sora 2 Cameo using <span class="bg-primary/20 text-primary px-1 rounded">@cameo</span> handle in your video description to add yourself or a friend into your generated videos.
                        </p>
                    </div>

                    <!-- Video Description -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-bold uppercase tracking-tight italic">Video Description <span class="text-red-500">*</span></label>
                            <button class="bg-muted px-3 py-1 rounded border border-border text-[10px] font-bold flex items-center gap-1.5 hover:bg-muted/80">
                                <i data-lucide="sparkles" class="w-3 h-3 text-primary"></i>
                                GET SUGGESTIONS
                            </button>
                        </div>
                        <textarea x-model="topic" placeholder="Describe the video you want to generate..." rows="6" class="flex min-h-[140px] w-full rounded-lg border border-input bg-muted/30 px-4 py-3 text-sm focus:ring-1 focus:ring-primary"></textarea>
                        <p class="text-[10px] text-muted-foreground text-right italic">0 / 1000 characters</p>
                    </div>

                    <!-- Source Image -->
                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight">Source Image <span class="text-muted-foreground ml-1">(Optional)</span></label>
                        <div class="flex gap-3">
                            <input x-model="sourceImage" type="text" placeholder="Enter Image URL..." class="flex-1 h-12 rounded-lg border border-input bg-muted/30 px-4 text-sm">
                            <button class="bg-muted border border-border rounded-lg px-6 h-12 text-sm font-bold flex items-center gap-2 hover:bg-muted/80 transition-colors">
                                <i data-lucide="upload" class="w-4 h-4 text-primary"></i>
                                Upload
                            </button>
                        </div>
                        <p class="text-[10px] text-muted-foreground italic">Coming soon - Image-to-video generation will be available in a future update.</p>
                    </div>

                    <!-- Settings Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight italic">AI Model</label>
                            <select x-model="aiModel" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm focus:ring-1 focus:ring-primary">
                                <option>Sora 2 BEST</option>
                            </select>
                            <p class="text-[10px] text-muted-foreground italic">Best for highly realistic videos with precise physics</p>
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight italic">Duration (7 tokens)</label>
                            <select x-model="videoDuration" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                                <option>10 seconds (7 tokens)</option>
                                <option>15 seconds (10 tokens)</option>
                            </select>
                            <p class="text-[10px] text-muted-foreground italic">Range: 10-15s</p>
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight italic">Resolution</label>
                            <select x-model="resolution" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm focus:ring-1 focus:ring-primary">
                                <option>1080p</option>
                            </select>
                            <p class="text-[10px] text-muted-foreground italic">Only 1080p supported</p>
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-tight italic">Aspect Ratio</label>
                            <select x-model="aspectRatio" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm focus:ring-1 focus:ring-primary">
                                <option>Portrait</option>
                                <option>Square</option>
                                <option>Widescreen</option>
                            </select>
                        </div>
                    </div>

                    <!-- Shared Parameters (Integrated) -->
                    <div class="space-y-6 pt-6 border-t border-border/50">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-primary italic">Prompt Call to Action</label>
                            <input x-model="cta" type="text" placeholder="e.g., 'Click the link in bio for more info'" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                                <input type="checkbox" x-model="addLineBreaks" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold leading-none uppercase tracking-tight">Include Script Breaks</span>
                                    <i data-lucide="help-circle" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                                <input type="checkbox" x-model="includeHashtags" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold leading-none uppercase tracking-tight">Include Hashtags</span>
                                    <i data-lucide="hash" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="text-xs text-muted-foreground mt-4 italic">
                        Estimated Token Consumption: <span class="font-bold text-foreground" x-text="videoDuration === '15 seconds (10 tokens)' ? 10 : 7"></span>
                    </div>

                    <!-- Insufficient Tokens Alert (Placeholder) -->
                    <div class="border border-red-200 bg-red-50 rounded-lg p-4 flex items-center gap-4">
                        <div class="bg-red-100 p-2 rounded-full">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-700">Insufficient Tokens</p>
                            <p class="text-xs text-red-600 italic">You need 7 tokens to generate this video, but you only have 0 tokens. <a href="#" class="font-bold underline">Upgrade your plan</a> to get more tokens.</p>
                        </div>
                    </div>

                    <!-- Bottom Button -->
                    <div class="pt-2">
                        <button @click="generateContent" :disabled="isGenerating" class="w-full h-14 bg-primary hover:opacity-90 text-primary-foreground rounded-lg font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:pointer-events-none">
                            <template x-if="!isGenerating">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                                    <span>Generate Video (<span x-text="videoDuration === '15 seconds (10 tokens)' ? 10 : 7"></span> tokens)</span>
                                </div>
                            </template>
                            <template x-if="isGenerating">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                    <span>Architecting Video...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Blog Generator Interface -->
                <div x-show="generator === 'blog'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-6" style="display: none;">
                    
                    <!-- Blog Header (Top Level) -->
                    <div class="mb-2">
                        <div class="flex items-center gap-3 mb-1">
                            <i data-lucide="book" class="w-6 h-6 text-primary"></i>
                            <h2 class="text-2xl font-black text-foreground">Blog Generator</h2>
                        </div>
                        <p class="text-sm text-muted-foreground font-medium">Generate SEO-optimized blog posts and publish them directly to your WordPress site</p>
                    </div>

                    <!-- WordPress Notice -->
                    <div class="bg-muted/30 border border-border rounded-lg p-4 flex gap-4 items-center">
                        <div class="bg-card w-10 h-10 rounded-lg border border-border flex items-center justify-center shrink-0">
                            <i data-lucide="info" class="w-5 h-5 text-primary"></i>
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-foreground mb-0.5">WordPress Not Connected</p>
                            <p class="text-muted-foreground italic">Please connect your WordPress account in <a href="#" class="text-primary font-bold underline">Social Connections</a> to post blogs.</p>
                        </div>
                    </div>

                    <!-- Token Notice -->
                    <div class="bg-muted/30 border border-border rounded-lg p-4 flex gap-4 items-center">
                        <div class="bg-white/50 w-10 h-10 rounded-lg border border-border flex items-center justify-center shrink-0">
                            <i data-lucide="info" class="w-5 h-5 text-primary"></i>
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-foreground mb-0.5">Token Cost</p>
                            <p class="text-muted-foreground italic">Each blog generation costs 20 tokens. You currently have 0 tokens.</p>
                        </div>
                    </div>

                    <!-- Main Form Container -->
                    <div class="border border-border rounded-2xl bg-muted/5 p-8 space-y-8 relative overflow-hidden">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
                                <h3 class="text-xl font-black text-foreground">Blog Post Generator</h3>
                            </div>
                            <p class="text-xs text-muted-foreground font-medium">Generate SEO-optimized blog posts with Google Trends insights</p>
                        </div>

                        <!-- Mode Selector Tabs -->
                        <div class="bg-muted/50 rounded-lg p-1 flex gap-1 border border-border/50">
                            <button @click="isBatchMode = false" :class="!isBatchMode ? 'bg-white shadow-sm text-foreground' : 'text-muted-foreground hover:bg-white/50'" class="flex-1 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all">Single Blog</button>
                            <button @click="isBatchMode = true" :class="isBatchMode ? 'bg-white shadow-sm text-foreground' : 'text-muted-foreground hover:bg-white/50'" class="flex-1 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all">Batch Generate</button>
                        </div>

                        <!-- Suggestions Box -->
                        <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 space-y-4">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="sparkles" class="w-4 h-4 text-primary"></i>
                                <h4 class="text-xs font-black text-primary uppercase tracking-wider">Suggested Blog Topics</h4>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Enter keyword for blog topic suggestions (e.g., 'home improvement', 'fitness')" class="flex-1 h-12 rounded-lg border border-primary/20 bg-white/50 px-4 text-xs italic focus:ring-1 focus:ring-primary">
                                <button class="h-12 px-6 rounded-lg bg-white border border-primary/30 text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-primary/5">
                                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                    Get Suggestions
                                </button>
                            </div>
                            <p class="text-[10px] text-muted-foreground font-medium italic opacity-70">Enter a keyword to get AI-generated blog topic suggestions related to your search. Leave blank to see general trending topics.</p>
                            <div class="text-center py-4 text-[10px] text-muted-foreground italic border-t border-primary/10">
                                Enter a keyword to get AI-generated suggestions or enter a topic manually below.
                            </div>
                        </div>

                        <!-- Input Fields -->
                        <div class="space-y-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic flex items-center gap-1">
                                    Blog Topic <span class="text-red-500">*</span>
                                </label>
                                <input x-model="topic" type="text" placeholder="e.g., How to Create Viral Social Media Content with AI" class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
                                <p class="text-[10px] text-muted-foreground font-medium italic">Enter the main topic or title for your blog post, or select from suggestions above</p>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
                                    SEO Keywords (comma-separated)
                                </label>
                                <input x-model="keywords" type="text" placeholder="e.g., AI content creation, social media marketing, viral posts" class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
                                <p class="text-[10px] text-muted-foreground font-medium italic">Optional: Add keywords you want to rank for (comma-separated)</p>
                            </div>
                        </div>

                        <!-- Featured Image Selection -->
                        <div class="bg-muted/10 border border-border rounded-xl p-6 space-y-4">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-foreground">Featured Image</h4>
                            <div class="flex gap-12">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" value="ai" x-model="featuredImageType" class="w-4 h-4 text-primary focus:ring-primary border-border">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="wand-2" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
                                        <span class="text-xs font-black uppercase tracking-tight text-foreground">AI Generated</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" value="manual" x-model="featuredImageType" class="w-4 h-4 text-primary focus:ring-primary border-border">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="upload" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
                                        <span class="text-xs font-black uppercase tracking-tight text-foreground">Upload My Own</span>
                                    </div>
                                </label>
                            </div>
                            <p class="text-[10px] text-muted-foreground font-medium italic opacity-70">An AI-generated featured image will be created automatically based on your blog topic.</p>
                        </div>

                        <!-- Shared Parameters (Integrated) -->
                        <div class="space-y-6 pt-6 border-t border-border/50">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-primary italic">Global Call to Action</label>
                                <input x-model="cta" type="text" placeholder="e.g., 'Read the full guide at arch-ai.io'" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                                    <input type="checkbox" x-model="addLineBreaks" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold leading-none uppercase tracking-tight">Structured Layout</span>
                                        <i data-lucide="help-circle" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="text-xs text-muted-foreground mt-4 italic">
                            Estimated Token Consumption: <span class="font-bold text-foreground">20 tokens</span>
                        </div>

                        <!-- Generate Button Area -->
                        <div class="pt-4">
                            <button @click="generateContent" :disabled="isGenerating" class="w-full h-14 bg-primary hover:opacity-90 text-primary-foreground rounded-xl font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3 text-xs disabled:opacity-50 disabled:pointer-events-none">
                                <template x-if="!isGenerating">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                                        <span>Generate & Preview Blog Post</span>
                                    </div>
                                </template>
                                <template x-if="isGenerating">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                        <span>Architecting Blog...</span>
                                    </div>
                                </template>
                            </button>
                        </div>

                        <!-- Technical Specs List -->
                        <div class="bg-primary/5 border border-primary/10 rounded-xl p-6 space-y-4">
                            <div class="flex items-center gap-2 mb-1">
                                <i data-lucide="layout" class="w-4 h-4 text-primary"></i>
                                <h4 class="text-[10px] font-black text-primary uppercase tracking-wider">AI Blog Generation Features:</h4>
                            </div>
                            <div class="grid grid-cols-1 gap-1.5">
                                <template x-for="feature in [
                                    'SEO-optimized content with proper keyword integration',
                                    'Comprehensive, valuable content (1500-2000 words)',
                                    'Proper heading structure for SEO',
                                    'Meta titles and descriptions included',
                                    'Direct posting to WordPress',
                                    'Google Trends integration for trending topics',
                                    'Batch generation for multiple posts at once'
                                ]">
                                    <div class="flex items-start gap-2.5">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-primary shrink-0 mt-0.5"></i>
                                        <p class="text-[10px] font-semibold text-muted-foreground leading-tight" x-text="feature"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar: Context Aware Content -->
        <div class="space-y-6">
            <!-- Normal Mode: Recent Content (Facebook Style Feed) -->
            <div x-show="generator !== 'video'" class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden">
                <div class="flex flex-col space-y-1.5 p-6 border-b border-border/50 bg-background/50 backdrop-blur-sm sticky top-0 z-10">
                    <h3 class="text-xl font-bold leading-none tracking-tight flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-primary"></i>
                        Recent Activity Feed
                    </h3>
                </div>
                
                <div class="bg-muted/5 p-4 min-h-[400px]">
                    <div class="flex flex-col gap-2">
                        @forelse($recentContents as $item)
                            @php
                                $statusColors = [
                                    'published' => 'text-green-700 bg-green-50/80 border-green-200',
                                    'draft' => 'text-amber-700 bg-amber-50/80 border-amber-200',
                                    'generating' => 'text-blue-700 bg-blue-50/80 border-blue-200',
                                    'failed' => 'text-red-700 bg-red-50/80 border-red-200',
                                    'scheduled' => 'text-purple-700 bg-purple-50/80 border-purple-200'
                                ];
                                $statusColor = $statusColors[$item->status] ?? 'text-slate-700 bg-slate-50 border-slate-200';
                                
                                $typeIcons = [
                                    'social-post' => 'share-2',
                                    'blog-post' => 'book-open',
                                    'video' => 'video',
                                    'email' => 'mail'
                                ];
                                $icon = $typeIcons[$item->type] ?? 'file-text';
                            @endphp
                            
                            <a href="{{ route('content-creator.show', $item) }}" 
                               class="group flex items-center gap-3 p-3 rounded-xl border border-border bg-card hover:bg-white hover:shadow-md hover:border-primary/30 transition-all duration-200">
                                
                                <!-- Icon / Indicator -->
                                <div class="w-10 h-10 rounded-lg bg-muted/50 border border-border flex items-center justify-center shrink-0 group-hover:bg-primary/5 group-hover:border-primary/10 transition-colors">
                                    <i data-lucide="{{ $icon }}" class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors"></i>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-foreground truncate group-hover:text-primary transition-colors">{{ $item->title }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-muted-foreground">{{ $item->created_at->diffForHumans() }}</span>
                                        <span class="text-muted-foreground/30 text-[10px]">•</span>
                                        <span class="text-[9px] font-bold uppercase tracking-widest {{ $statusColor }} px-1.5 py-0.5 rounded-md border">
                                            {{ $item->status }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Chevron -->
                                <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-primary"></i>
                                </div>
                            </a>
                        @empty
                            <!-- Empty State -->
                            <div class="text-center py-12 text-muted-foreground opacity-50 bg-card/50 rounded-xl border border-border border-dashed">
                                <i data-lucide="rss" class="w-10 h-10 mx-auto mb-3"></i>
                                <p class="text-sm font-medium">Activity feed is empty.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Video Mode: How It Works & Tokens -->
            <div x-show="generator === 'video'" x-transition:enter="transition duration-500" class="space-y-6" style="display: none;">
                <!-- How It Works -->
                <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden">
                    <div class="p-6 bg-muted/30 border-b border-border flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-4 h-4 text-primary"></i>
                        <h3 class="font-bold text-sm uppercase tracking-tighter">How It Works</h3>
                    </div>
                    <div class="p-6 space-y-6 text-xs">
                        <div class="flex gap-4">
                            <i data-lucide="layers" class="w-5 h-5 text-purple-500 shrink-0"></i>
                            <div>
                                <p class="font-bold mb-1">Queue System</p>
                                <p class="text-muted-foreground leading-relaxed italic">Videos generate in the background. You can leave the page and come back later.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <i data-lucide="save" class="w-5 h-5 text-blue-500 shrink-0"></i>
                            <div>
                                <p class="font-bold mb-1">Auto-Save</p>
                                <p class="text-muted-foreground leading-relaxed italic">All generated videos are automatically saved to your media gallery.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <i data-lucide="share-2" class="w-5 h-5 text-green-500 shrink-0"></i>
                            <div>
                                <p class="font-bold mb-1">Social Media Scheduling</p>
                                <p class="text-muted-foreground leading-relaxed italic">On the completed video page, you can schedule your videos to your social media accounts.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <i data-lucide="user-plus" class="w-5 h-5 text-amber-500 shrink-0"></i>
                            <div>
                                <p class="font-bold mb-1">Sora Cameo Tagging</p>
                                <p class="text-muted-foreground leading-relaxed italic">Tag your Sora 2 Cameo handle in your description to include yourself in the video.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Token Cost -->
                <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden">
                    <div class="p-6 bg-muted/30 border-b border-border flex items-center gap-2">
                        <i data-lucide="coins" class="w-4 h-4 text-amber-500"></i>
                        <h3 class="font-bold text-sm uppercase tracking-tighter">Token Cost</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-primary/5 border border-primary/20 rounded-lg p-3 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-primary">Cost</p>
                                <p class="text-[10px] text-muted-foreground italic">10-second video</p>
                            </div>
                            <p class="text-lg font-black text-primary">7 tokens</p>
                        </div>

                        <!-- Comparison Table -->
                        <div class="border border-border rounded-lg overflow-hidden bg-muted/10">
                            <div class="bg-green-50/50 p-2 border-b border-border flex items-center justify-center gap-2">
                                <i data-lucide="zap" class="w-3 h-3 text-green-600"></i>
                                <span class="text-[10px] font-bold text-green-700 uppercase tracking-widest">Pricing Analysis</span>
                            </div>
                            <table class="w-full text-[10px]">
                                <thead class="bg-muted/50">
                                    <tr class="text-left border-b border-border">
                                        <th class="p-2 font-bold opacity-60">Duration</th>
                                        <th class="p-2 font-bold text-primary">Architect</th>
                                        <th class="p-2 font-bold opacity-60 text-right">OpenAI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-border/10">
                                        <td class="p-2 opacity-80">10 seconds</td>
                                        <td class="p-2 font-black text-green-600">$0.34</td>
                                        <td class="p-2 opacity-80 text-right">$1.00</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 opacity-80">15 seconds</td>
                                        <td class="p-2 font-black text-green-600">$0.58</td>
                                        <td class="p-2 opacity-80 text-right">$1.50</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center px-1">
                                <p class="text-[10px] font-bold uppercase text-muted-foreground">Your Balance</p>
                                <p class="text-[10px] font-black uppercase text-foreground">0 Tokens</p>
                            </div>
                            <button class="w-full h-10 border border-border bg-card hover:bg-muted text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                                Get More Tokens
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
