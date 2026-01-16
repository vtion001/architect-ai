{{-- Document Builder - Source Content Input --}}
<div class="space-y-2">
    <label class="text-sm font-medium leading-none">Source Content / Context</label>
    <p class="text-[11px] text-muted-foreground mb-1">Paste raw notes or data here. AI will restructure it professionally into the template.</p>
    <textarea 
        x-model="sourceContent"
        class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        placeholder="e.g. Our company grew 20% this year. We launched 3 new products. John led the sales team well..."></textarea>
</div>
