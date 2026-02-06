{{--
    Video Style: Minimalist
    
    Clean, simple aesthetics with focus on core message
    Best for: Tech products, modern brands, B2B content
--}}

<div class="space-y-6">
    {{-- Style Description --}}
    <div class="bg-gradient-to-r from-slate-50 to-gray-50 rounded-xl p-5 border border-slate-200">
        <div class="flex items-start gap-3">
            <div class="bg-slate-100 p-2 rounded-lg">
                <i data-lucide="minus-circle" class="w-5 h-5 text-slate-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 mb-1">Minimalist Style</h3>
                <p class="text-xs text-slate-700 leading-relaxed">
                    Clean, distraction-free visuals with intentional use of white space. Perfect for tech products, 
                    modern brands, and content where clarity and focus are paramount.
                </p>
            </div>
        </div>
    </div>

    {{-- Minimalist-Specific Parameters --}}
    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Visual Approach
        </label>
        <select x-model="visualApproach" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="product-focused">Product-Focused (Clean Bg)</option>
            <option value="text-heavy">Text & Typography Heavy</option>
            <option value="geometric-shapes">Geometric Shapes & Lines</option>
            <option value="flat-design">Flat Design Illustrations</option>
            <option value="photography-simple">Simple Photography</option>
            <option value="screen-recording">Screen Recording (UI/UX)</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Background Style
        </label>
        <select x-model="backgroundStyle" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="pure-white">Pure White</option>
            <option value="pure-black">Pure Black</option>
            <option value="soft-gray">Soft Gray</option>
            <option value="single-color">Single Solid Color</option>
            <option value="subtle-gradient">Subtle Gradient (2 Colors Max)</option>
            <option value="soft-shadow">Soft Shadow Only</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Typography Focus
        </label>
        <select x-model="typographyFocus" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="none">Minimal/No Text</option>
            <option value="headline-only">Single Headline Only</option>
            <option value="headline-subtext">Headline + Subtext</option>
            <option value="step-by-step">Step-by-Step Instructions</option>
            <option value="key-stats">Key Statistics/Numbers</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Animation Type
        </label>
        <select x-model="animationType" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="none">No Animation (Static)</option>
            <option value="fade-in">Simple Fade In</option>
            <option value="slide-reveal">Smooth Slide Reveal</option>
            <option value="scale-up">Scale Up Entrance</option>
            <option value="text-typewriter">Text Typewriter Effect</option>
            <option value="morph">Shape Morph Transition</option>
        </select>
    </div>

    {{-- Color Scheme Selector --}}
    <div class="space-y-3">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Color Scheme (Max 2-3 Colors)
        </label>
        <div class="grid grid-cols-2 gap-3">
            <button @click="colorScheme = 'black-white'" 
                    :class="colorScheme === 'black-white' ? 'ring-2 ring-primary' : ''"
                    class="h-12 rounded-lg flex items-center justify-center gap-2 border border-border hover:scale-105 transition-transform">
                <div class="w-6 h-6 rounded-full bg-black"></div>
                <div class="w-6 h-6 rounded-full bg-white border"></div>
                <span class="text-xs font-medium">B&W</span>
            </button>
            <button @click="colorScheme = 'tech-blue'" 
                    :class="colorScheme === 'tech-blue' ? 'ring-2 ring-primary' : ''"
                    class="h-12 rounded-lg flex items-center justify-center gap-2 border border-border hover:scale-105 transition-transform">
                <div class="w-6 h-6 rounded-full bg-blue-600"></div>
                <div class="w-6 h-6 rounded-full bg-white border"></div>
                <span class="text-xs font-medium">Tech</span>
            </button>
            <button @click="colorScheme = 'nature-green'" 
                    :class="colorScheme === 'nature-green' ? 'ring-2 ring-primary' : ''"
                    class="h-12 rounded-lg flex items-center justify-center gap-2 border border-border hover:scale-105 transition-transform">
                <div class="w-6 h-6 rounded-full bg-green-600"></div>
                <div class="w-6 h-6 rounded-full bg-white border"></div>
                <span class="text-xs font-medium">Nature</span>
            </button>
            <button @click="colorScheme = 'luxury-gold'" 
                    :class="colorScheme === 'luxury-gold' ? 'ring-2 ring-primary' : ''"
                    class="h-12 rounded-lg flex items-center justify-center gap-2 border border-border hover:scale-105 transition-transform">
                <div class="w-6 h-6 rounded-full bg-amber-500"></div>
                <div class="w-6 h-6 rounded-full bg-black"></div>
                <span class="text-xs font-medium">Luxury</span>
            </button>
        </div>
    </div>

    {{-- Advanced Minimalist Options --}}
    <div class="bg-muted/30 rounded-xl p-5 space-y-4">
        <h4 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="layout" class="w-4 h-4"></i>
            Composition Settings
        </h4>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Centered Composition</label>
            <input type="checkbox" x-model="centeredComposition" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Grid/Alignment Guides</label>
            <input type="checkbox" x-model="gridGuides" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Generous White Space (Breathing Room)</label>
            <input type="checkbox" x-model="whiteSpace" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Sans-Serif Typography Only</label>
            <input type="checkbox" x-model="sansSerifOnly" class="rounded border-border text-primary focus:ring-primary">
        </div>

        <div class="space-y-2 pt-2">
            <label class="text-xs font-medium text-foreground">Element Count (Fewer = More Minimalist)</label>
            <div class="flex items-center gap-4">
                <input type="range" x-model="elementCount" min="1" max="5" step="1" class="flex-1">
                <span class="text-xs font-mono bg-muted px-2 py-1 rounded" x-text="elementCount + ' elements'"></span>
            </div>
        </div>
    </div>

    {{-- Layout Preview --}}
    <div class="space-y-3">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Layout Preview
        </label>
        <div class="grid grid-cols-3 gap-2">
            <button @click="layout = 'centered'" 
                    :class="layout === 'centered' ? 'ring-2 ring-primary' : ''"
                    class="aspect-video bg-white border-2 border-border rounded-lg flex items-center justify-center hover:scale-105 transition-transform">
                <div class="w-8 h-8 bg-slate-800 rounded"></div>
            </button>
            <button @click="layout = 'left-aligned'" 
                    :class="layout === 'left-aligned' ? 'ring-2 ring-primary' : ''"
                    class="aspect-video bg-white border-2 border-border rounded-lg flex items-start justify-start p-2 hover:scale-105 transition-transform">
                <div class="w-6 h-6 bg-slate-800 rounded"></div>
            </button>
            <button @click="layout = 'split-screen'" 
                    :class="layout === 'split-screen' ? 'ring-2 ring-primary' : ''"
                    class="aspect-video bg-white border-2 border-border rounded-lg grid grid-cols-2 hover:scale-105 transition-transform">
                <div class="bg-slate-800"></div>
                <div class="bg-white"></div>
            </button>
        </div>
        <p class="text-xs text-muted-foreground italic capitalize" x-text="'Layout: ' + (layout || 'centered').replace('-', ' ')"></p>
    </div>

    {{-- Minimalist Tips --}}
    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
        <div class="flex items-start gap-2">
            <i data-lucide="target" class="w-4 h-4 text-indigo-600 mt-0.5"></i>
            <div class="text-xs text-indigo-900 space-y-1">
                <p class="font-semibold">Minimalist Design Principles:</p>
                <ul class="list-disc list-inside space-y-0.5 ml-2">
                    <li>"Less is more" - remove everything non-essential</li>
                    <li>Use negative space intentionally</li>
                    <li>Limit color palette to 2-3 colors maximum</li>
                    <li>Choose clean, modern sans-serif fonts</li>
                    <li>Every element must serve a purpose</li>
                    <li>Align elements to invisible grid for harmony</li>
                </ul>
            </div>
        </div>
    </div>
</div>
