{{--
    Video Style: Cinematic
    
    High-quality, professional cinematography with dramatic lighting
    Best for: Brand stories, product showcases, emotional narratives
--}}

<div class="space-y-6">
    {{-- Style Description --}}
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-5 border border-purple-200">
        <div class="flex items-start gap-3">
            <div class="bg-purple-100 p-2 rounded-lg">
                <i data-lucide="film" class="w-5 h-5 text-purple-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-purple-900 mb-1">Cinematic Style</h3>
                <p class="text-xs text-purple-700 leading-relaxed">
                    Hollywood-quality visuals with dramatic lighting, smooth camera movements, and professional color grading. 
                    Perfect for brand films, luxury products, and emotionally-driven storytelling.
                </p>
            </div>
        </div>
    </div>

    {{-- Cinematic-Specific Parameters --}}
    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Cinematic Mood
        </label>
        <select x-model="cinematicMood" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="epic">Epic & Inspirational</option>
            <option value="dramatic">Dramatic & Intense</option>
            <option value="serene">Serene & Peaceful</option>
            <option value="mysterious">Mysterious & Suspenseful</option>
            <option value="romantic">Romantic & Emotional</option>
            <option value="dystopian">Dystopian & Dark</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Camera Movement Type
        </label>
        <select x-model="cameraMovement" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="drone-aerial">Drone Aerial Shot</option>
            <option value="crane-up">Crane/Jib Movement (Rising)</option>
            <option value="dolly-zoom">Dolly Zoom (Vertigo Effect)</option>
            <option value="tracking-shot">Smooth Tracking Shot</option>
            <option value="orbit">360° Orbital Shot</option>
            <option value="slow-pan">Slow Panoramic Pan</option>
            <option value="static-wide">Static Wide Angle</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Lighting Setup
        </label>
        <select x-model="lightingSetup" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="golden-hour">Golden Hour (Warm Sunset)</option>
            <option value="blue-hour">Blue Hour (Cool Twilight)</option>
            <option value="harsh-shadows">Harsh Shadows (High Contrast)</option>
            <option value="soft-diffused">Soft Diffused Light</option>
            <option value="dramatic-side">Dramatic Side Lighting</option>
            <option value="backlit">Backlit Silhouette</option>
            <option value="neon-night">Neon/Urban Night</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Color Grading Preset
        </label>
        <select x-model="colorGrading" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="teal-orange">Teal & Orange (Blockbuster)</option>
            <option value="desaturated-blue">Desaturated Blue (Thriller)</option>
            <option value="warm-vintage">Warm Vintage (Nostalgic)</option>
            <option value="high-contrast-bw">High Contrast B&W</option>
            <option value="vibrant-saturated">Vibrant & Saturated</option>
            <option value="muted-pastel">Muted Pastel Tones</option>
            <option value="cyberpunk">Cyberpunk Neon</option>
        </select>
    </div>

    {{-- Advanced Cinematic Options --}}
    <div class="bg-muted/30 rounded-xl p-5 space-y-4">
        <h4 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="clapperboard" class="w-4 h-4"></i>
            Cinematic Effects
        </h4>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Lens Flares</label>
            <input type="checkbox" x-model="lensFlares" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Film Grain Texture</label>
            <input type="checkbox" x-model="filmGrain" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Depth of Field Blur</label>
            <input type="checkbox" x-model="depthOfField" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Motion Blur</label>
            <input type="checkbox" x-model="motionBlur" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Anamorphic Aspect (2.39:1)</label>
            <input type="checkbox" x-model="anamorphic" class="rounded border-border text-primary focus:ring-primary">
        </div>
    </div>

    {{-- Cinematic Tips --}}
    <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
        <div class="flex items-start gap-2">
            <i data-lucide="award" class="w-4 h-4 text-amber-600 mt-0.5"></i>
            <div class="text-xs text-amber-900 space-y-1">
                <p class="font-semibold">Pro Cinematic Tips:</p>
                <ul class="list-disc list-inside space-y-0.5 ml-2">
                    <li>Use wide-angle establishing shots for scale</li>
                    <li>Slow-motion enhances dramatic moments</li>
                    <li>Rule of thirds for balanced composition</li>
                    <li>Leading lines guide viewer's eye</li>
                    <li>Consistent color palette tells visual story</li>
                </ul>
            </div>
        </div>
    </div>
</div>
