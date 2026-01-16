{{-- Loading Overlay for Document Generation --}}
{{-- Expects parent x-data with: isGenerating, isLoadingPreview, generateStage, generateProgress, stageTitle --}}
<div x-show="isGenerating || isLoadingPreview" x-cloak class="absolute inset-0 bg-black/70 backdrop-blur-md z-50 flex items-center justify-center rounded-[40px]">
    <div class="text-center space-y-8 max-w-md px-8">
        {{-- Spinner --}}
        <div class="relative inline-block">
            <div class="w-24 h-24 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
            <i data-lucide="sparkles" class="w-10 h-10 text-primary absolute inset-0 m-auto animate-pulse"></i>
        </div>
        
        {{-- Stage Title --}}
        <div>
            <p class="text-lg font-black uppercase tracking-wide text-white mb-2" 
               x-text="stageTitle"></p>
            <p class="text-xs text-white/60 italic">Your document is being crafted with AI precision</p>
        </div>
        
        {{-- Progress Bar --}}
        <template x-if="isGenerating && generateStage">
            <div class="space-y-4">
                {{-- Bar --}}
                <div class="w-64 mx-auto h-2 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-primary to-primary/80 rounded-full transition-all duration-300 ease-out" 
                         :style="`width: ${Math.min(generateProgress, 100)}%`"></div>
                </div>
                
                {{-- Stage Steps --}}
                <div class="flex justify-between text-[9px] uppercase tracking-widest font-bold">
                    <span :class="generateProgress > 10 ? 'text-primary' : 'text-white/30'">
                        <i data-lucide="check" class="w-3 h-3 inline" x-show="generateProgress > 20"></i>
                        Init
                    </span>
                    <span :class="generateProgress > 30 ? 'text-primary' : 'text-white/30'">
                        <i data-lucide="check" class="w-3 h-3 inline" x-show="generateProgress > 50"></i>
                        Analyze
                    </span>
                    <span :class="generateProgress > 60 ? 'text-primary' : 'text-white/30'">
                        <i data-lucide="check" class="w-3 h-3 inline" x-show="generateProgress > 80"></i>
                        Generate
                    </span>
                    <span :class="generateProgress > 85 ? 'text-primary' : 'text-white/30'">
                        <i data-lucide="check" class="w-3 h-3 inline" x-show="generateProgress >= 100"></i>
                        Render
                    </span>
                </div>
                
                {{-- Percentage --}}
                <p class="mono text-[10px] font-black text-primary/80" x-text="Math.round(Math.min(generateProgress, 100)) + '% Complete'"></p>
                
                {{-- Safe to Navigate Notice --}}
                <div class="pt-4 border-t border-white/10">
                    <p class="text-[10px] text-white/50 flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" class="w-3 h-3 text-green-400"></i>
                        Safe to navigate away — document will be saved to 
                        <a href="/documents" class="text-primary underline hover:text-primary/80">Documents</a>
                    </p>
                </div>
            </div>
        </template>
        
        {{-- Simple loading for preview --}}
        <template x-if="isLoadingPreview && !isGenerating">
            <p class="mono text-[10px] font-black uppercase tracking-[0.4em] text-primary animate-pulse">Loading Preview...</p>
        </template>
    </div>
</div>
