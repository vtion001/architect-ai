{{-- Document Blueprints Form Section --}}
{{-- 
    @param $modelPrefix - Either 'newBrand' or 'selectedBrand' for x-model binding
    @param $tabVariable - The x-data variable name for tab state (e.g., 'activeBlueprintTab' or 'editBlueprintTab')
    @param $inputIdPrefix - Prefix for file input IDs (e.g., 'create' or 'edit')
    @param $isEdit - Whether this is for edit mode (affects analyzeBlueprint call)
--}}
@props(['modelPrefix' => 'newBrand', 'tabVariable' => 'activeBlueprintTab', 'inputIdPrefix' => 'create', 'isEdit' => false])

<div class="space-y-6 pt-6 border-t border-border/50" x-data="{ {{ $tabVariable }}: 'proposal' }">
    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
        <i data-lucide="file-text" class="w-3 h-3"></i> Document Blueprints
    </h3>
    <p class="text-[10px] text-muted-foreground italic">Define strict templates for specific document types (e.g. Monsterbug Contracts).</p>

    {{-- Tab Buttons --}}
    <div class="flex gap-2 p-1 bg-muted/30 rounded-xl mb-4">
        <template x-for="type in ['proposal', 'contract', 'executive-summary']">
            <button @click="{{ $tabVariable }} = type" 
                    :class="{{ $tabVariable }} === type ? 'bg-white shadow-sm text-primary' : 'text-muted-foreground'"
                    class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all"
                    x-text="type.replace('-', ' ')">
            </button>
        </template>
    </div>

    <div class="space-y-6 animate-in fade-in duration-300">
        {{-- AI Auto-Fill --}}
        <div class="bg-primary/5 border border-primary/20 rounded-xl p-4 flex items-center justify-between">
            <div class="flex-1 mr-4">
                <h4 class="text-[10px] font-black uppercase text-primary mb-1">AI Auto-Fill</h4>
                <p class="text-[10px] text-muted-foreground">Upload an existing PDF/Text document to automatically extract these fields.</p>
                <input type="file" 
                       :id="'{{ $inputIdPrefix }}_' + {{ $tabVariable }} + '_upload'" 
                       accept=".pdf,.txt,.md" 
                       class="mt-2 block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
            </div>
            <button @click="analyzeBlueprint({{ $tabVariable }}, {{ $isEdit ? 'true' : 'false' }})" 
                    :disabled="isAnalyzing" 
                    class="shrink-0 h-8 px-4 rounded-lg bg-primary text-primary-foreground text-[10px] font-black uppercase tracking-widest shadow-lg disabled:opacity-50 flex items-center gap-2">
                <template x-if="isAnalyzing"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i></template>
                <span x-text="isAnalyzing ? 'Scanning...' : 'Extract'"></span>
            </button>
        </div>

        {{-- Blueprint Fields --}}
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Standard Introduction (Boilerplate)</label>
            <textarea x-model="{{ $modelPrefix }}.blueprints[{{ $tabVariable }}].boilerplate_intro" 
                      rows="3" 
                      placeholder="We would like to thank you very sincerely for..." 
                      class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Core Scope / Terms (Static Text)</label>
            <textarea x-model="{{ $modelPrefix }}.blueprints[{{ $tabVariable }}].scope_of_work_template" 
                      rows="5" 
                      placeholder="A. SOIL TREATMENT... B. DRILLING..." 
                      class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Legal Clauses / Payment Terms</label>
            <textarea x-model="{{ $modelPrefix }}.blueprints[{{ $tabVariable }}].legal_terms" 
                      rows="3" 
                      placeholder="NOTE: All chemicals and equipment... Terms of Payment..." 
                      class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Structure Instruction for AI</label>
            <input x-model="{{ $modelPrefix }}.blueprints[{{ $tabVariable }}].structure_instruction" 
                   type="text" 
                   placeholder="Use Monsterbug standard layout: Intro -> Scope -> Pricing -> Terms." 
                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
        </div>
    </div>
</div>
