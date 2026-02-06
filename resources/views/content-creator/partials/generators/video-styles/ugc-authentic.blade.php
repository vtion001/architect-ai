{{--
    Video Style: UGC / Authentic
    
    User-generated content style with raw, authentic feel
    Best for: Social proof, testimonials, behind-the-scenes
--}}

<div class="space-y-6">
    {{-- Style Description --}}
    <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-5 border border-orange-200">
        <div class="flex items-start gap-3">
            <div class="bg-orange-100 p-2 rounded-lg">
                <i data-lucide="smartphone" class="w-5 h-5 text-orange-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-orange-900 mb-1">UGC / Authentic Style</h3>
                <p class="text-xs text-orange-700 leading-relaxed">
                    Raw, unfiltered content that feels genuine and relatable. Perfect for building trust with authentic storytelling, 
                    testimonials, or showing real people using your product.
                </p>
            </div>
        </div>
    </div>

    {{-- UGC-Specific Parameters --}}
    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            UGC Scenario
        </label>
        <select x-model="ugcScenario" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="testimonial">Customer Testimonial</option>
            <option value="unboxing">Product Unboxing</option>
            <option value="tutorial">Quick Tutorial/How-To</option>
            <option value="day-in-life">Day in the Life</option>
            <option value="before-after">Before & After</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Shooting Environment
        </label>
        <select x-model="environment" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="indoor-natural">Indoor with Natural Light</option>
            <option value="indoor-artificial">Indoor with Artificial Light</option>
            <option value="outdoor-day">Outdoor Daytime</option>
            <option value="car-interior">Car Interior</option>
            <option value="workspace">Home Office/Workspace</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Camera Movement
        </label>
        <select x-model="cameraMovement" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="handheld">Handheld (Natural Shake)</option>
            <option value="static">Static/Tripod</option>
            <option value="walking">Walking POV</option>
            <option value="selfie">Selfie Mode</option>
        </select>
    </div>

    {{-- Advanced UGC Options --}}
    <div class="bg-muted/30 rounded-xl p-5 space-y-4">
        <h4 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
            Authenticity Settings
        </h4>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Add Natural Imperfections</label>
            <input type="checkbox" x-model="addImperfections" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Include Background Noise</label>
            <input type="checkbox" x-model="includeNoise" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Casual Framing (Not Centered)</label>
            <input type="checkbox" x-model="casualFraming" class="rounded border-border text-primary focus:ring-primary">
        </div>
    </div>

    {{-- Tips for UGC --}}
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <div class="flex items-start gap-2">
            <i data-lucide="lightbulb" class="w-4 h-4 text-blue-600 mt-0.5"></i>
            <div class="text-xs text-blue-800 space-y-1">
                <p class="font-semibold">Tips for Better UGC Videos:</p>
                <ul class="list-disc list-inside space-y-0.5 ml-2">
                    <li>Use first-person perspective for authenticity</li>
                    <li>Include natural pauses and "ums" for realism</li>
                    <li>Show real reactions, not scripted performances</li>
                    <li>Use everyday language, not marketing speak</li>
                </ul>
            </div>
        </div>
    </div>
</div>
