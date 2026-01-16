{{-- Document Builder - Strategic Mandate Input --}}
<div class="space-y-2">
    <label class="text-sm font-medium leading-none">Strategic Mandate / Instructions</label>
    <p class="text-[11px] text-muted-foreground mb-1">Describe the specific goal or additional instructions for the AI analyst.</p>
    <textarea 
        x-model="prompt"
        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        placeholder="e.g. Focus on ROI for the next 5 years and include a risk assessment..."></textarea>
</div>
