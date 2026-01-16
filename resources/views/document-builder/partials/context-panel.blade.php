{{-- Supplementary Context Panel --}}
{{-- 
    Expects parent x-data with:
    template, sourceContent, isGenerating, isParsing, targetRole,
    draftCoverLetter(), parseResume()
--}}
<div class="bg-card border border-border rounded-[40px] p-8 shadow-sm relative overflow-hidden">
    <div class="absolute inset-0 grid-canvas pointer-events-none opacity-10"></div>
    <div class="flex items-center justify-between mb-6 relative z-10">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 italic">Supplementary Context</h3>
        
        <div class="flex gap-2">
            {{-- Draft with AI (Cover Letter Assistant) --}}
            <button x-show="template === 'cover-letter'" 
                    @click="draftCoverLetter"
                    :disabled="isGenerating || !sourceContent || !targetRole"
                    class="flex items-center gap-2 px-3 py-1.5 bg-purple-500/10 text-purple-400 rounded-lg hover:bg-purple-500/20 transition-all disabled:opacity-30">
                <i data-lucide="sparkles" class="w-3 h-3"></i>
                <span class="text-[9px] font-black uppercase tracking-widest">Draft with AI</span>
            </button>

            {{-- Resume Uploader --}}
            <div x-show="template === 'cv-resume' || template === 'cover-letter'" class="relative">
                <input type="file" id="resumeUpload" class="hidden" accept=".pdf,.txt,.md,.docx" @change="parseResume">
                <label for="resumeUpload" class="cursor-pointer flex items-center gap-2 px-3 py-1.5 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors">
                    <template x-if="!isParsing">
                        <div class="flex items-center gap-2">
                            <i data-lucide="upload-cloud" class="w-3 h-3"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest">Import PDF</span>
                        </div>
                    </template>
                    <template x-if="isParsing">
                        <div class="flex items-center gap-2">
                            <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest">Parsing...</span>
                        </div>
                    </template>
                </label>
            </div>
        </div>
    </div>
    <textarea x-model="sourceContent" rows="8" placeholder="Inject raw data or specific session notes..."
              class="w-full bg-muted/20 border border-border rounded-3xl p-6 text-xs font-medium italic focus:ring-2 focus:ring-primary/20 outline-none relative z-10"></textarea>
    <div x-show="template === 'cover-letter'" class="mt-3 px-2">
        <p class="text-[9px] text-muted-foreground italic">Tip: Import your CV first, then click 'Draft with AI' to build your story.</p>
    </div>
</div>
