{{-- Sender Identity (Proposal/Report/Contract) --}}
{{-- Expects parent x-data with: template, senderName, senderTitle, contractDetails, fetchPreview() --}}
<div class="pt-6 border-t border-border/50" x-show="template !== 'cv-resume' && template !== 'cover-letter'">
    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 mb-4 block">Sender Identity</label>
    <div class="grid grid-cols-1 gap-4">
        <input x-model="senderName" @input.debounce.800ms="fetchPreview" type="text" placeholder="Sender Name"
               class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
        <input x-model="senderTitle" @input.debounce.800ms="fetchPreview" type="text" placeholder="Professional Title (e.g. Founder & CEO)"
               class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
        
        {{-- Contract Provider Details --}}
        <div x-show="template === 'contract'" class="space-y-4 pt-2" x-transition>
            <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Provider Legal Details</label>
            <input x-model="contractDetails.providerBusiness" @input.debounce.800ms="fetchPreview" type="text" placeholder="Registered Business Name (if different)"
                   class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
            <input x-model="contractDetails.providerAddress" @input.debounce.800ms="fetchPreview" type="text" placeholder="Provider Address"
                   class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
            <input x-model="contractDetails.providerTaxId" @input.debounce.800ms="fetchPreview" type="text" placeholder="Provider Tax ID / EIN"
                   class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
        </div>
    </div>
</div>
