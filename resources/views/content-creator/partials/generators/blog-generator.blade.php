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
                       placeholder="Enter keyword for blog topic suggestions (e.g., 'home improvement', 'fitness')" 
                       class="flex-1 h-12 rounded-lg border border-primary/20 bg-white/50 px-4 text-xs italic focus:ring-1 focus:ring-primary">
                <button class="h-12 px-6 rounded-lg bg-white border border-primary/30 text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-primary/5">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    Get Suggestions
                </button>
            </div>
            <p class="text-[10px] text-muted-foreground font-medium italic opacity-70">
                Enter a keyword to get AI-generated blog topic suggestions related to your search. Leave blank to see general trending topics.
            </p>
            <div class="text-center py-4 text-[10px] text-muted-foreground italic border-t border-primary/10">
                Enter a keyword to get AI-generated suggestions or enter a topic manually below.
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
                <input x-model="keywords" type="text" 
                       placeholder="e.g., AI content creation, social media marketing, viral posts" 
                       class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
                <p class="text-[10px] text-muted-foreground font-medium italic">Optional: Add keywords you want to rank for (comma-separated)</p>
            </div>
        </div>

        {{-- Featured Image Selection --}}
        <div class="bg-muted/10 border border-border rounded-xl p-6 space-y-4">
            <h4 class="text-[10px] font-black uppercase tracking-widest text-foreground">Featured Image</h4>
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
            <p class="text-[10px] text-muted-foreground font-medium italic opacity-70">
                An AI-generated featured image will be created automatically based on your blog topic.
            </p>
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
    </div>
</div>
