{{--
    Video Style: 3D Animation
    
    Computer-generated 3D animated content
    Best for: Product demos, explainers, abstract concepts
--}}

<div class="space-y-6">
    {{-- Style Description --}}
    <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl p-5 border border-cyan-200">
        <div class="flex items-start gap-3">
            <div class="bg-cyan-100 p-2 rounded-lg">
                <i data-lucide="box" class="w-5 h-5 text-cyan-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-cyan-900 mb-1">3D Animation Style</h3>
                <p class="text-xs text-cyan-700 leading-relaxed">
                    Computer-generated 3D animated visuals with unlimited creative possibilities. Perfect for product visualizations, 
                    abstract concepts, and scenarios impossible to film in reality.
                </p>
            </div>
        </div>
    </div>

    {{-- 3D Animation-Specific Parameters --}}
    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Animation Style
        </label>
        <select x-model="animationStyle" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="photorealistic">Photorealistic (Cinema 4D Style)</option>
            <option value="stylized-cartoon">Stylized Cartoon (Pixar Style)</option>
            <option value="low-poly">Low-Poly Geometric</option>
            <option value="clay-render">Clay/Plasteline Render</option>
            <option value="glass-transparent">Glass & Transparent Materials</option>
            <option value="wireframe">Wireframe Technical</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Scene Environment
        </label>
        <select x-model="sceneEnvironment" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="infinite-white">Infinite White Studio</option>
            <option value="infinite-black">Infinite Black Void</option>
            <option value="gradient-backdrop">Gradient Backdrop</option>
            <option value="geometric-shapes">Floating Geometric Shapes</option>
            <option value="neon-grid">Neon Grid (Synthwave)</option>
            <option value="nature-scene">3D Nature Environment</option>
            <option value="futuristic-city">Futuristic Cityscape</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Camera Animation
        </label>
        <select x-model="cameraAnimation" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="orbit-360">360° Product Orbit</option>
            <option value="reveal-zoom">Zoom In Reveal</option>
            <option value="exploded-view">Exploded Assembly View</option>
            <option value="fly-through">Fly-Through Scene</option>
            <option value="morph-transition">Morph/Transition Between Objects</option>
            <option value="isometric-rotate">Isometric Rotation</option>
        </select>
    </div>

    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Material & Texture
        </label>
        <select x-model="material" 
                class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
            <option value="metallic-chrome">Metallic Chrome</option>
            <option value="brushed-metal">Brushed Metal</option>
            <option value="glossy-plastic">Glossy Plastic</option>
            <option value="matte-rubber">Matte Rubber</option>
            <option value="glass-crystal">Glass & Crystal</option>
            <option value="gold-luxury">Gold Luxury Finish</option>
            <option value="holographic">Holographic Iridescent</option>
            <option value="wood-natural">Wood Natural Texture</option>
        </select>
    </div>

    {{-- Advanced 3D Options --}}
    <div class="bg-muted/30 rounded-xl p-5 space-y-4">
        <h4 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="layers" class="w-4 h-4"></i>
            3D Effects & Physics
        </h4>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Ray-Traced Reflections</label>
            <input type="checkbox" x-model="rayTracing" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Global Illumination</label>
            <input type="checkbox" x-model="globalIllumination" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Particle Effects (Dust/Smoke)</label>
            <input type="checkbox" x-model="particleEffects" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Physics Simulation (Gravity/Bounce)</label>
            <input type="checkbox" x-model="physicsSimulation" class="rounded border-border text-primary focus:ring-primary">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-foreground">Caustics (Light Through Glass)</label>
            <input type="checkbox" x-model="caustics" class="rounded border-border text-primary focus:ring-primary">
        </div>

        <div class="space-y-2 pt-2">
            <label class="text-xs font-medium text-foreground">Animation Speed</label>
            <div class="flex items-center gap-4">
                <input type="range" x-model="animationSpeed" min="0.5" max="2" step="0.1" class="flex-1">
                <span class="text-xs font-mono bg-muted px-2 py-1 rounded" x-text="animationSpeed + 'x'"></span>
            </div>
        </div>
    </div>

    {{-- Color Palette Selector --}}
    <div class="space-y-3">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Color Palette
        </label>
        <div class="grid grid-cols-4 gap-2">
            <button @click="colorPalette = 'vibrant'" 
                    :class="colorPalette === 'vibrant' ? 'ring-2 ring-primary' : ''"
                    class="h-10 rounded-lg bg-gradient-to-r from-pink-400 via-purple-400 to-blue-400 hover:scale-105 transition-transform">
            </button>
            <button @click="colorPalette = 'pastel'" 
                    :class="colorPalette === 'pastel' ? 'ring-2 ring-primary' : ''"
                    class="h-10 rounded-lg bg-gradient-to-r from-pink-200 via-purple-200 to-blue-200 hover:scale-105 transition-transform">
            </button>
            <button @click="colorPalette = 'monochrome'" 
                    :class="colorPalette === 'monochrome' ? 'ring-2 ring-primary' : ''"
                    class="h-10 rounded-lg bg-gradient-to-r from-gray-700 via-gray-500 to-gray-300 hover:scale-105 transition-transform">
            </button>
            <button @click="colorPalette = 'neon'" 
                    :class="colorPalette === 'neon' ? 'ring-2 ring-primary' : ''"
                    class="h-10 rounded-lg bg-gradient-to-r from-green-400 via-cyan-400 to-pink-400 hover:scale-105 transition-transform">
            </button>
        </div>
        <p class="text-xs text-muted-foreground italic" x-text="'Selected: ' + colorPalette.charAt(0).toUpperCase() + colorPalette.slice(1)"></p>
    </div>

    {{-- 3D Tips --}}
    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
        <div class="flex items-start gap-2">
            <i data-lucide="sparkles" class="w-4 h-4 text-green-600 mt-0.5"></i>
            <div class="text-xs text-green-900 space-y-1">
                <p class="font-semibold">3D Animation Best Practices:</p>
                <ul class="list-disc list-inside space-y-0.5 ml-2">
                    <li>Simple animations render faster and look cleaner</li>
                    <li>Use smooth easing for natural motion</li>
                    <li>Lighting makes or breaks 3D realism</li>
                    <li>Product demos work best with 360° orbits</li>
                    <li>Abstract concepts shine with geometric shapes</li>
                </ul>
            </div>
        </div>
    </div>
</div>
