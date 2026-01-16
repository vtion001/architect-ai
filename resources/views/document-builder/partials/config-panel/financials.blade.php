{{-- Project Financials (Proposal & Contract) --}}
{{-- Expects parent x-data with: template, financials --}}
<div class="space-y-4 pt-6 border-t border-border/50" x-show="template === 'proposal' || template === 'contract'" x-transition>
    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
        <i data-lucide="dollar-sign" class="w-3 h-3"></i>
        Project Financials
    </label>
    
    {{-- Total Investment --}}
    <div class="grid grid-cols-3 gap-2">
        <div class="col-span-2">
            <input x-model="financials.totalInvestment" type="number" placeholder="Total Investment"
                    class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[11px] font-bold outline-none">
        </div>
        <div>
            <input x-model="financials.currency" type="text" placeholder="USD"
                    class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[11px] font-bold outline-none">
        </div>
    </div>
    
    {{-- Timeline --}}
    <div class="grid grid-cols-1 gap-2">
        <input x-model="financials.timeline" type="text" placeholder="e.g. 4-5 weeks"
               class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[11px] font-bold outline-none">
    </div>
    
    {{-- Payment Milestones --}}
    <div class="space-y-2 pt-2">
        <label class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground italic px-1">Payment Milestones</label>
        <template x-for="(milestone, index) in financials.paymentMilestones" :key="index">
            <div class="flex items-center gap-2">
                <input x-model="milestone.name" type="text" placeholder="Milestone Name" class="flex-grow h-9 bg-muted/20 border border-border rounded-md px-3 text-[10px] font-medium">
                <input x-model="milestone.percentage" type="number" placeholder="%" class="w-16 h-9 bg-muted/20 border border-border rounded-md px-3 text-[10px] font-medium">
                <button @click="financials.paymentMilestones.splice(index, 1)" class="text-red-500/70 hover:text-red-500 p-1">
                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                </button>
            </div>
        </template>
        <button @click="financials.paymentMilestones.push({name: '', percentage: null})" class="text-xs font-black uppercase tracking-widest text-primary/80 hover:text-primary transition-colors flex items-center gap-1 pt-2">
            <i data-lucide="plus-circle" class="w-3 h-3"></i>
            Add Milestone
        </button>
    </div>
</div>
