{{-- Post Generator Interface Partial --}}
<div x-show="generator === 'post'" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 transform -translate-y-2" 
     x-transition:enter-end="opacity-100 transform translate-y-0" 
     class="space-y-8">
    
    {{-- Post Header --}}
    <div class="mb-2">
        <div class="flex items-center gap-3 mb-1">
            <i data-lucide="edit-3" class="w-6 h-6 text-primary"></i>
            <h2 class="text-2xl font-black text-foreground">Post Architect</h2>
        </div>
        <p class="text-sm text-muted-foreground font-medium">Define parameters for high-engagement text posts powered by your knowledge base.</p>
    </div>

    {{-- Brand Persona --}}
    <div class="space-y-3" x-show="brands.length > 0">
        <label class="text-[10px] font-black uppercase tracking-widest text-primary italic flex items-center gap-2">
            <i data-lucide="fingerprint" class="w-3 h-3"></i>
            Brand Persona
        </label>
        <div class="relative">
            <select x-model="selectedBrandId" 
                    class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-bold focus:ring-1 focus:ring-primary appearance-none cursor-pointer hover:bg-muted/30 transition-colors">
                <option value="">No Brand (Generic Voice)</option>
                <template x-for="brand in brands" :key="brand.id">
                    <option :value="brand.id" x-text="brand.name"></option>
                </template>
            </select>
            <i data-lucide="chevron-down" class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none"></i>
        </div>
    </div>

    {{-- Main Topic --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
                Post Topic / Theme <span class="text-red-500">*</span>
            </label>
            <button @click="fetchSuggestions()" 
                    :disabled="isLoadingSuggestions || !topic" 
                    class="bg-muted px-3 py-1 rounded border border-border text-[10px] font-bold flex items-center gap-1.5 hover:bg-muted/80 disabled:opacity-50">
                <span x-show="!isLoadingSuggestions" class="flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-3 h-3 text-primary"></i>
                    GET SUGGESTIONS
                </span>
                <span x-show="isLoadingSuggestions">Running OpenAI...</span>
            </button>
        </div>
        <input x-model="topic" type="text" 
               placeholder="e.g., 'Modern Architecture Trends 2026'" 
               class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
        
        {{-- Suggestions Results --}}
        <div x-show="suggestions" x-transition 
             class="p-4 bg-muted/30 border border-border rounded-lg relative">
            <button @click="suggestions = ''" 
                    class="absolute top-2 right-2 text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-3 h-3"></i>
            </button>
            
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-bold uppercase text-primary">OpenAI Ideas:</h4>
                <template x-if="kbDiscovered > 0">
                    <div class="flex items-center gap-1.5 px-2 py-1 rounded-md bg-green-500/10 border border-green-500/20 text-green-600 animate-pulse">
                        <i data-lucide="database" class="w-3 h-3"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest" x-text="kbDiscovered + ' Context Assets Discovered'"></span>
                    </div>
                </template>
            </div>

            <div class="prose prose-sm max-w-none text-muted-foreground whitespace-pre-wrap text-sm" x-text="suggestions"></div>
        </div>
    </div>

    {{-- Parameters Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Quantity</label>
            <input x-model="count" type="number" min="1" max="100" 
                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Tone</label>
            <select x-model="tone" 
                    class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                <option>Professional</option>
                <option>Casual</option>
                <option>Provocative</option>
                <option>Empathetic</option>
            </select>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Format</label>
            <select x-model="type" 
                    class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                <option value="social-media">Social Media</option>
                <option value="email">Direct Email</option>
                <option value="ad-copy">Ad Copy</option>
            </select>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
                Mandate / Specific Context
            </label>
            <button @click="refineContext()" 
                    :disabled="!context || isRefining" 
                    class="bg-muted px-3 py-1 rounded border border-border text-[10px] font-bold flex items-center gap-1.5 hover:bg-muted/80 disabled:opacity-50">
                <span x-show="!isRefining" class="flex items-center gap-1.5">
                    <i data-lucide="wand-2" class="w-3 h-3 text-primary"></i>
                    AI REWRITE ASSIST
                </span>
                <span x-show="isRefining">Polishing...</span>
            </button>
        </div>
        <textarea x-model="context" 
                  placeholder="e.g., 'Focus on sustainable materials and eco-friendly designs...'" 
                  rows="4" 
                  class="w-full min-h-[120px] bg-muted/20 border border-border rounded-xl px-5 py-4 text-sm font-medium focus:ring-1 focus:ring-primary"></textarea>
    </div>

    {{-- Shared Parameters --}}
    <div class="space-y-6 pt-6 border-t border-border/50">
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label class="text-[10px] font-black uppercase tracking-widest text-primary/80 italic">
                    Global Call to Action
                </label>
                @include('content-creator.partials.cta-snippets-dropdown')
            </div>
            <input x-model="cta" type="text" 
                   placeholder="e.g., 'Click the link in bio for more info'" 
                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                <input type="checkbox" x-model="addLineBreaks" 
                       class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold leading-none uppercase tracking-tight">Include Script Breaks</span>
                    <i data-lucide="help-circle" class="w-3.5 h-3.5 text-muted-foreground"></i>
                </div>
            </label>
            <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                <input type="checkbox" x-model="includeHashtags" 
                       class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold leading-none uppercase tracking-tight">Include Hashtags</span>
                    <i data-lucide="hash" class="w-3.5 h-3.5 text-muted-foreground"></i>
                </div>
            </label>
        </div>
    </div>

    {{-- Bottom Button --}}
    <div class="pt-4">
        <button @click="generateContent" 
                :disabled="isGenerating" 
                class="w-full h-14 bg-primary hover:opacity-90 text-primary-foreground rounded-xl font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3 text-xs disabled:opacity-50 disabled:pointer-events-none">
            <template x-if="!isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                    <span>Generate Text Content</span>
                </div>
            </template>
            <template x-if="isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <span>Architecting Posts...</span>
                </div>
            </template>
        </button>
    </div>
</div>