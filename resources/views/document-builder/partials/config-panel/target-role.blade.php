{{-- Target Role (CV & Cover Letter) --}}
{{-- Expects parent x-data with: template, targetRole, jobDescription --}}
<div class="space-y-3" x-show="template === 'cv-resume' || template === 'cover-letter'" x-transition>
    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
        <i data-lucide="crosshair" class="w-3 h-3"></i>
        Target Role / Job Title
    </label>
    <textarea x-model="targetRole" placeholder="e.g. Senior Product Manager" rows="1"
           class="w-full bg-muted/20 border border-border rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none resize-none"></textarea>
    
    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2 mt-4">
        <i data-lucide="file-text" class="w-3 h-3"></i>
        Paste Job Description (Recommended)
    </label>
    <textarea x-model="jobDescription" placeholder="Paste the full job offer description here for AI tailoring..." rows="4"
           class="w-full bg-muted/20 border border-border rounded-2xl p-4 text-xs font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
    <p class="text-[9px] text-muted-foreground italic px-1">AI will analyze keywords from this description to optimize your document.</p>
</div>
