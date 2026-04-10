{{-- Blog Generator Interface Partial --}}
<div x-show="generator === 'blog'" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 transform -translate-y-2" 
     x-transition:enter-end="opacity-100 transform translate-y-0" 
     class="space-y-6" style="display: none;">
    
    {{-- Blog Header --}}
    <div class="mb-2">
        <div class="flex items-center gap-3 mb-1">
            <i data-lucide="book" class="w-6 h-6 text-primary"></i>
            <h2 class="text-2xl font-black text-foreground">Blog Generator</h2>
        </div>
        <p class="text-sm text-muted-foreground font-medium">
            Generate SEO-optimized blog posts and publish them directly to your WordPress site
        </p>
    </div>

    {{-- WordPress Notice --}}
    <div class="bg-muted/30 border border-border rounded-lg p-4 flex gap-4 items-center">
        <div class="bg-card w-10 h-10 rounded-lg border border-border flex items-center justify-center shrink-0">
            <i data-lucide="info" class="w-5 h-5 text-primary"></i>
        </div>
        <div class="text-xs">
            <p class="font-bold text-foreground mb-0.5">WordPress Not Connected</p>
            <p class="text-muted-foreground italic">
                Please connect your WordPress account in <a href="#" class="text-primary font-bold underline">Social Connections</a> to post blogs.
            </p>
        </div>
    </div>

    {{-- Token Notice --}}
    <div class="bg-muted/30 border border-border rounded-lg p-4 flex gap-4 items-center">
        <div class="bg-white/50 w-10 h-10 rounded-lg border border-border flex items-center justify-center shrink-0">
            <i data-lucide="info" class="w-5 h-5 text-primary"></i>
        </div>
        <div class="text-xs">
            <p class="font-bold text-foreground mb-0.5">Token Cost</p>
            <p class="text-muted-foreground italic">Each blog generation costs 20 tokens. You currently have 0 tokens.</p>
        </div>
    </div>

    {{-- Main Form Container --}}
    <div class="border border-border rounded-2xl bg-muted/5 p-8 space-y-8 relative overflow-hidden">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
                <h3 class="text-xl font-black text-foreground">Blog Post Generator</h3>
            </div>
            <p class="text-xs text-muted-foreground font-medium">
                Generate SEO-optimized blog posts with Google Trends insights
            </p>
        </div>

        {{-- Mode Selector Tabs --}}
        <div class="bg-muted/50 rounded-lg p-1 flex gap-1 border border-border/50">
            <button @click="isBatchMode = false" 
                    :class="!isBatchMode ? 'bg-white shadow-sm text-foreground' : 'text-muted-foreground hover:bg-white/50'" 
                    class="flex-1 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all">
                Single Blog
            </button>
            <button @click="isBatchMode = true" 
                    :class="isBatchMode ? 'bg-white shadow-sm text-foreground' : 'text-muted-foreground hover:bg-white/50'" 
                    class="flex-1 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider transition-all">
                Batch Generate
            </button>
        </div>

        {{-- Suggestions Box --}}
        <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 space-y-4">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-primary"></i>
                <h4 class="text-xs font-black text-primary uppercase tracking-wider">Suggested Blog Topics</h4>
            </div>
            <div class="flex gap-2">
                <input type="text"
                       x-model="blogSuggestionKeyword"
                       @keydown.enter.prevent="fetchBlogSuggestions()"
                       placeholder="Enter keyword for blog topic suggestions (e.g., 'home improvement', 'fitness')"
                       class="flex-1 h-12 rounded-lg border border-primary/20 bg-white/50 px-4 text-xs italic focus:ring-1 focus:ring-primary">
                <button @click="fetchBlogSuggestions()"
                        :disabled="isLoadingBlogSuggestions"
                        class="h-12 px-6 rounded-lg bg-white border border-primary/30 text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-primary/5 disabled:opacity-50">
                    <template x-if="!isLoadingBlogSuggestions">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    </template>
                    <template x-if="isLoadingBlogSuggestions">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i>
                    </template>
                    Get Suggestions
                </button>
            </div>
            <p class="text-[10px] text-muted-foreground font-medium italic opacity-70">
                Enter a keyword to get AI-generated blog topic suggestions related to your search. Leave blank to see general trending topics.
            </p>
            <div class="text-center py-4 text-[10px] text-muted-foreground italic border-t border-primary/10"
                 x-show="!blogSuggestions || blogSuggestions.length === 0">
                Enter a keyword to get AI-generated suggestions or enter a topic manually below.
            </div>
            <div class="space-y-2 border-t border-primary/10 pt-4" x-show="blogSuggestions && blogSuggestions.length > 0">
                <template x-for="(suggestion, index) in blogSuggestions" :key="index">
                    <div @click="topic = suggestion.title; blogSuggestions = []"
                         class="flex items-start gap-3 p-3 rounded-lg bg-white/50 hover:bg-white cursor-pointer border border-transparent hover:border-primary/20 transition-all">
                        <i data-lucide="file-text" class="w-4 h-4 text-primary shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-foreground truncate" x-text="suggestion.title"></p>
                            <p class="text-[10px] text-muted-foreground mt-1 line-clamp-2" x-text="suggestion.description"></p>
                            <div class="flex gap-2 mt-2">
                                <span class="text-[9px] font-black uppercase tracking-wider text-primary/70" x-text="suggestion.category || 'General'"></span>
                                <span class="text-[9px] font-black uppercase tracking-wider text-muted-foreground" x-show="suggestion.search_volume" x-text="suggestion.search_volume"></span>
                            </div>
                        </div>
                        <i data-lucide="plus-circle" class="w-4 h-4 text-primary/50 hover:text-primary shrink-0"></i>
                    </div>
                </template>
            </div>
        </div>

        {{-- Input Fields --}}
        <div class="space-y-6">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic flex items-center gap-1">
                    Blog Topic <span class="text-red-500">*</span>
                </label>
                <input x-model="topic" type="text" 
                       placeholder="e.g., How to Create Viral Social Media Content with AI" 
                       class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
                <p class="text-[10px] text-muted-foreground font-medium italic">
                    Enter the main topic or title for your blog post, or select from suggestions above
                </p>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
                    SEO Keywords (comma-separated)
                </label>
                <div class="flex gap-2">
                    <input x-model="keywords" type="text"
                           @input="lastAutoSeoTopic = null"
                           placeholder="e.g., AI content creation, social media marketing, viral posts"
                           class="flex-1 h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
                    <button @click="fetchSeoSuggestions()"
                            :disabled="isLoadingSeoSuggestions"
                            class="h-14 px-5 rounded-xl bg-white border border-border text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-muted/50 disabled:opacity-50">
                        <template x-if="!isLoadingSeoSuggestions">
                            <div class="flex items-center gap-2">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                <span>Sugggest</span>
                            </div>
                        </template>
                        <template x-if="isLoadingSeoSuggestions">
                            <div class="flex items-center gap-2">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                            </div>
                        </template>
                    </button>
                </div>
                <div x-show="seoSuggestions && seoSuggestions.length > 0" class="mt-2 p-3 bg-muted/30 border border-border rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-muted-foreground">Suggested keywords:</span>
                        <button @click="seoSuggestions = []" class="text-[9px] text-muted-foreground hover:text-foreground">Clear</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(kw, idx) in seoSuggestions" :key="idx">
                            <button @click="appendKeyword(kw)"
                                    class="px-3 py-1.5 text-[10px] font-medium bg-white border border-primary/20 rounded-full hover:bg-primary/5 hover:border-primary/40 transition-colors"
                                    x-text="kw"></button>
                        </template>
                    </div>
                </div>
                <p class="text-[10px] text-muted-foreground font-medium italic">Optional: Add keywords you want to rank for (comma-separated)</p>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic flex items-center gap-1">
                        Blog Post Body
                    </label>
                    <button @click="generateBlogBody()"
                            :disabled="isGeneratingBlogBody || !topic"
                            class="h-14 px-5 rounded-xl bg-white border border-border text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-muted/50 disabled:opacity-50">
                        <template x-if="!isGeneratingBlogBody">
                            <div class="flex items-center gap-2">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                <span>Generate Body</span>
                            </div>
                        </template>
                        <template x-if="isGeneratingBlogBody">
                            <div class="flex items-center gap-2">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                                <span>Generating...</span>
                            </div>
                        </template>
                    </button>
                </div>
                <textarea x-model="blogBody"
                          rows="8"
                          placeholder="Enter your blog post content here, or leave blank to auto-generate the full post..."
                          class="w-full bg-muted/20 border border-border rounded-xl px-5 py-4 text-sm font-medium focus:ring-1 focus:ring-primary resize-none"></textarea>
                <p class="text-[10px] text-muted-foreground font-medium italic">
                    Optional: Paste an existing draft or notes. Leave blank to generate a complete SEO-optimized blog post.
                </p>
            </div>
        </div>

        {{-- Featured Image Selection --}}
        <div class="bg-muted/10 border border-border rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-foreground">Featured Image</h4>
                <div x-show="featuredImageType === 'ai'" class="flex items-center gap-2">
                    <span class="text-[9px] font-bold text-purple-600 uppercase tracking-wider">Banana Pro</span>
                    <span class="text-[9px] text-muted-foreground">//</span>
                    <span class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider">OpenAI</span>
                </div>
            </div>
            <div class="flex gap-12">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="radio" value="ai" x-model="featuredImageType"
                           class="w-4 h-4 text-primary focus:ring-primary border-border">
                    <div class="flex items-center gap-2">
                        <i data-lucide="wand-2" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
                        <span class="text-xs font-black uppercase tracking-tight text-foreground">AI Generated</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="radio" value="manual" x-model="featuredImageType"
                           class="w-4 h-4 text-primary focus:ring-primary border-border">
                    <div class="flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
                        <span class="text-xs font-black uppercase tracking-tight text-foreground">Upload My Own</span>
                    </div>
                </label>
            </div>
            <div x-show="featuredImageType === 'ai'">
                <button @click="generateFeaturedImage()"
                        class="w-full h-14 px-5 rounded-xl bg-white border border-border text-primary text-[10px] font-black uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-muted/50">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Generate Featured Image</span>
                </button>
                <p class="text-[10px] text-muted-foreground font-medium italic opacity-70 mt-2">
                    AI generates an image prompt from your blog content, then creates the featured image.
                </p>
                <div x-show="featuredImageUrl" class="mt-4 p-3 bg-primary/5 rounded-xl border border-primary/20">
                    <div class="flex items-center gap-3">
                        <img :src="featuredImageUrl" class="w-16 h-16 rounded-lg object-cover">
                        <div class="flex-1">
                            <p class="text-[10px] font-black uppercase text-primary tracking-widest mb-1">Image Ready</p>
                            <p class="text-[10px] text-muted-foreground italic">Your featured image has been generated.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="featuredImageType === 'manual'" class="space-y-3">
                <div x-show="!featuredImageUrl"
                     class="relative border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-primary/50 transition-colors cursor-pointer"
                     :class="{ 'border-red-400': featuredImageUploadError }"
                     @click="$refs.featuredImageInput.click()"
                     @dragover.prevent="isDraggingFeaturedImage = true"
                     @dragleave.prevent="isDraggingFeaturedImage = false"
                     @drop.prevent="handleFeaturedImageDrop($event)">
                    <input type="file"
                           x-ref="featuredImageInput"
                           @change="handleFeaturedImageUpload($event)"
                           accept="image/*"
                           class="hidden">
                    <div class="flex flex-col items-center gap-3">
                        <i data-lucide="upload" class="w-10 h-10 text-muted-foreground"></i>
                        <div>
                            <p class="text-sm font-bold text-foreground">Click to upload or drag and drop</p>
                            <p class="text-[10px] text-muted-foreground mt-1">PNG, JPG, WEBP up to 10MB</p>
                        </div>
                    </div>
                </div>
                <div x-show="isUploadingFeaturedImage" class="flex items-center justify-center py-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-primary"></i>
                        <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Uploading...</span>
                    </div>
                </div>
                <div x-show="featuredImageUrl && featuredImageType === 'manual'" class="p-3 bg-primary/5 rounded-xl border border-primary/20">
                    <div class="flex items-center gap-3">
                        <img :src="featuredImageUrl" class="w-16 h-16 rounded-lg object-cover">
                        <div class="flex-1">
                            <p class="text-[10px] font-black uppercase text-primary tracking-widest mb-1">Image Uploaded</p>
                            <p class="text-[10px] text-muted-foreground italic">Your featured image is ready.</p>
                        </div>
                        <button @click="featuredImageUrl = ''" class="w-8 h-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i>
                        </button>
                    </div>
                </div>
                <p x-show="featuredImageUploadError" class="text-[10px] text-red-500 font-bold" x-text="featuredImageUploadError"></p>
            </div>
        </div>

        {{-- Shared Parameters --}}
        <div class="space-y-6 pt-6 border-t border-border/50">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic">Global Call to Action</label>
                    @include('content-creator.partials.cta-snippets-dropdown')
                </div>
                <input x-model="cta" type="text" 
                       placeholder="e.g., 'Read the full guide at arch-ai.io'" 
                       class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                    <input type="checkbox" x-model="addLineBreaks" 
                           class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
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

        {{-- Generate Button Area --}}
        <div class="pt-4">
            <button @click="generateContent" 
                    :disabled="isGenerating" 
                    class="w-full h-14 bg-primary hover:opacity-90 text-primary-foreground rounded-xl font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3 text-xs disabled:opacity-50 disabled:pointer-events-none">
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

        {{-- Technical Specs List --}}
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

        {{-- Banana Pro Image Creator Modal --}}
        @include('content-creator.partials.post-card.modals.image-creator-modal')
    </div>
</div>
