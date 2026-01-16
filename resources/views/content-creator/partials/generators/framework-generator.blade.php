{{-- Framework Calendar Generator Interface Partial --}}
<div x-show="generator === 'framework'" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 transform -translate-y-2" 
     x-transition:enter-end="opacity-100 transform translate-y-0" 
     class="space-y-8" style="display: none;">
    
    <div class="mb-2">
        <div class="flex items-center gap-3 mb-1">
            <i data-lucide="calendar" class="w-6 h-6 text-primary"></i>
            <h2 class="text-2xl font-black text-foreground">Content Framework</h2>
        </div>
        <p class="text-sm text-muted-foreground font-medium">
            Generate a complete weekly content plan based on the 4-Pillar Strategy.
        </p>
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

    {{-- Topic --}}
    <div class="space-y-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">
            Core Topic / Niche <span class="text-red-500">*</span>
        </label>
        <input x-model="topic" type="text" 
               placeholder="e.g., 'Sustainable Interior Design'" 
               class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
    </div>

    {{-- Info Box - 4-Pillar Strategy --}}
    <div class="bg-primary/5 border border-primary/10 rounded-xl p-6 space-y-4">
        <div class="flex items-center gap-2 mb-1">
            <i data-lucide="layout-grid" class="w-4 h-4 text-primary"></i>
            <h4 class="text-[10px] font-black text-primary uppercase tracking-wider">The 4-Pillar Strategy:</h4>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="p-3 bg-white/5 rounded-lg border border-white/10">
                <span class="text-[9px] font-black text-blue-400 uppercase">Educational (3x)</span>
                <p class="text-[10px] text-muted-foreground mt-1">Build authority with how-to's and insights.</p>
            </div>
            <div class="p-3 bg-white/5 rounded-lg border border-white/10">
                <span class="text-[9px] font-black text-purple-400 uppercase">Showcase (2x)</span>
                <p class="text-[10px] text-muted-foreground mt-1">Demonstrate expertise with case studies.</p>
            </div>
            <div class="p-3 bg-white/5 rounded-lg border border-white/10">
                <span class="text-[9px] font-black text-green-400 uppercase">Conversational (2x)</span>
                <p class="text-[10px] text-muted-foreground mt-1">Build community with polls and questions.</p>
            </div>
            <div class="p-3 bg-white/5 rounded-lg border border-white/10">
                <span class="text-[9px] font-black text-amber-400 uppercase">Promotional (1x)</span>
                <p class="text-[10px] text-muted-foreground mt-1">Drive conversions with offers.</p>
            </div>
        </div>
    </div>

    {{-- Generate Button --}}
    <div class="pt-4">
        <button @click="generateContent" 
                :disabled="isGenerating" 
                class="w-full h-14 bg-primary hover:opacity-90 text-primary-foreground rounded-xl font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3 text-xs disabled:opacity-50 disabled:pointer-events-none">
            <template x-if="!isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                    <span>Generate Weekly Calendar</span>
                </div>
            </template>
            <template x-if="isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <span>Architecting Strategy...</span>
                </div>
            </template>
        </button>
    </div>
</div>
