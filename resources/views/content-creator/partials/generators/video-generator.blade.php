{{-- Video Generator Interface Partial (REFACTORED) --}}
<div x-show="generator === 'video'" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 transform -translate-y-2" 
     x-transition:enter-end="opacity-100 transform translate-y-0" 
     class="space-y-8" style="display: none;">
    
    {{-- Video Header --}}
    <div class="mb-2">
        <div class="flex items-center gap-3 mb-1">
            <i data-lucide="video" class="w-6 h-6 text-primary"></i>
            <h2 class="text-2xl font-black text-foreground">Video Architect</h2>
        </div>
        <p class="text-sm text-muted-foreground font-medium">Create AI-generated short-form videos with Sora 2.</p>
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

    {{-- Main Topic / Description --}}
    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Video Subject / Prompt <span class="text-red-500">*</span>
        </label>
        <textarea x-model="topic" 
                  placeholder="e.g., 'Cinematic drone shot of a futuristic eco-friendly city at sunset...'" 
                  rows="3" 
                  class="w-full bg-muted/20 border border-border rounded-xl px-5 py-4 text-sm font-medium focus:ring-1 focus:ring-primary"></textarea>
    </div>

    {{-- Video Style Selector --}}
    <div class="space-y-3">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Visual Style</label>
        <div class="grid grid-cols-2 gap-3">
            <button @click="videoStyle = 'UGC'" 
                    :class="videoStyle === 'UGC' ? 'bg-orange-100 border-orange-400 text-orange-900' : 'bg-muted/20 border-border text-foreground'"
                    class="h-20 border-2 rounded-xl flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all">
                <i data-lucide="smartphone" class="w-5 h-5"></i>
                <span class="text-xs font-bold">UGC / Authentic</span>
            </button>
            <button @click="videoStyle = 'Cinematic'" 
                    :class="videoStyle === 'Cinematic' ? 'bg-purple-100 border-purple-400 text-purple-900' : 'bg-muted/20 border-border text-foreground'"
                    class="h-20 border-2 rounded-xl flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all">
                <i data-lucide="film" class="w-5 h-5"></i>
                <span class="text-xs font-bold">Cinematic</span>
            </button>
            <button @click="videoStyle = 'Animation'" 
                    :class="videoStyle === 'Animation' ? 'bg-cyan-100 border-cyan-400 text-cyan-900' : 'bg-muted/20 border-border text-foreground'"
                    class="h-20 border-2 rounded-xl flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all">
                <i data-lucide="box" class="w-5 h-5"></i>
                <span class="text-xs font-bold">3D Animation</span>
            </button>
            <button @click="videoStyle = 'Minimalist'" 
                    :class="videoStyle === 'Minimalist' ? 'bg-slate-100 border-slate-400 text-slate-900' : 'bg-muted/20 border-border text-foreground'"
                    class="h-20 border-2 rounded-xl flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all">
                <i data-lucide="minus-circle" class="w-5 h-5"></i>
                <span class="text-xs font-bold">Minimalist</span>
            </button>
        </div>
    </div>

    {{-- Style-Specific Parameters (Dynamically Loaded) --}}
    @include('content-creator.partials.generators.video-style-router')

    {{-- Common Video Parameters --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Duration</label>
            <select x-model="videoDuration" 
                    class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                <option>10 seconds (7 tokens)</option>
                <option>15 seconds (10 tokens)</option>
            </select>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Aspect Ratio</label>
            <select x-model="aspectRatio" 
                    class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                <option value="Portrait">9:16 (Reels/TikTok)</option>
                <option value="Landscape">16:9 (YouTube)</option>
                <option value="Square">1:1 (Feed)</option>
            </select>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Model Version</label>
            <select x-model="aiModel" 
                    class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                <option>Sora 2 BEST</option>
                <option>Sora 2 Fast</option>
            </select>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Resolution</label>
            <select x-model="resolution" 
                    class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                <option value="1080p">1080p (Full HD)</option>
                <option value="720p">720p (HD)</option>
                <option value="4K">4K (Ultra HD)</option>
            </select>
        </div>
    </div>

    {{-- Insufficient Tokens Alert --}}
    <div class="border border-red-200 bg-red-50 rounded-lg p-4 flex items-center gap-4">
        <div class="bg-red-100 p-2 rounded-full">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-red-700">Insufficient Tokens</p>
            <p class="text-xs text-red-600 italic">
                You need <span x-text="videoDuration === '15 seconds (10 tokens)' ? 10 : 7"></span> tokens to generate this video, but you only have 0 tokens. 
                <a href="#" class="font-bold underline">Upgrade your plan</a> to get more tokens.
            </p>
        </div>
    </div>

    {{-- Generate Button --}}
    <div class="pt-2">
        <button @click="generateContent" 
                :disabled="isGenerating" 
                class="w-full h-14 bg-primary hover:opacity-90 text-primary-foreground rounded-lg font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:pointer-events-none">
            <template x-if="!isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                    <span>Generate Video (<span x-text="videoDuration === '15 seconds (10 tokens)' ? 10 : 7"></span> tokens)</span>
                </div>
            </template>
            <template x-if="isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <span>Rendering Video...</span>
                </div>
            </template>
        </button>
    </div>
</div>
