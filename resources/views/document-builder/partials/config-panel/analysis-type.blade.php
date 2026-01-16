{{-- Analysis Type / Intelligence Objective --}}
{{-- Expects parent x-data with: template, analysisType, availableObjectives --}}
<div class="space-y-3" x-show="template !== 'cv-resume'">
    <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic px-1">Intelligence Objective</label>
    <select x-model="analysisType" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
        <template x-for="objective in availableObjectives" :key="objective">
            <option x-text="objective" :value="objective"></option>
        </template>
    </select>
</div>
