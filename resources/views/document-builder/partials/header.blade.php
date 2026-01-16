{{-- Document Builder Header --}}
{{-- 
    Expects parent x-data context with:
    - htmlPreview, isGenerating, generateProgress, stageShortLabel
    - saveToKb(), generateReport()
--}}
<div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
    <div>
        <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground mb-2">Document Architect</h1>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Protocol: RAG-Injected Document Engine</span>
            </div>
            <span class="text-[10px] font-mono text-slate-400 uppercase tracking-tighter italic">Treasury Cost: 30 Tokens per build</span>
        </div>
    </div>
    
    <div class="flex gap-3">
        <button @click="saveToKb" :disabled="!htmlPreview || isGenerating" 
                class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all disabled:opacity-50">
            <i data-lucide="database" class="w-4 h-4"></i>
            Index to Hub
        </button>
        <button @click="generateReport" :disabled="isGenerating"
                class="h-14 px-10 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all disabled:opacity-50 min-w-[200px]">
            <template x-if="!isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Initiate Build</span>
                </div>
            </template>
            <template x-if="isGenerating">
                <div class="flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    <span x-text="stageShortLabel"></span>
                    <span class="text-primary-foreground/70" x-text="Math.round(Math.min(generateProgress, 100)) + '%'"></span>
                </div>
            </template>
        </button>
    </div>
</div>
