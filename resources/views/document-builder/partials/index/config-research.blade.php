{{-- Document Builder - Research Topic Input --}}
<div class="space-y-2">
    <label class="text-sm font-medium leading-none flex items-center gap-2">
        Deep Research Topic
        <span class="inline-flex items-center rounded-full border px-1.5 py-0.5 text-[10px] font-semibold bg-primary/10 text-primary border-transparent">Research Engine</span>
    </label>
    <div class="relative">
        <input 
            x-model="researchTopic"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-9 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
            placeholder="e.g. Market trends for semiconductor industry in Q3 2024..." />
        <i data-lucide="brain" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary opacity-70"></i>
    </div>
</div>
