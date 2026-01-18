{{-- Video Generator Interface Partial --}}
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

    {{-- Parameters Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Visual Style</label>
            <select x-model="videoStyle" 
                    class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                <option value="UGC">UGC / Authentic</option>
                <option value="Cinematic">Cinematic</option>
                <option value="Animation">3D Animation</option>
                <option value="Minimalist">Minimalist</option>
            </select>
        </div>
        
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
