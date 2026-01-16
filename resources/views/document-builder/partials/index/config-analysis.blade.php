{{-- Document Builder - Analysis Type Selection --}}
<div class="space-y-2">
    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
        Analysis Type
    </label>
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
            <span x-text="analysisType">Comparative Analysis</span>
            <i data-lucide="chevron-down" class="h-4 w-4 opacity-50"></i>
        </button>
        <div x-show="open" @click.away="open = false" class="absolute z-50 mt-1 w-full rounded-md border bg-popover p-1 text-popover-foreground shadow-md outline-none" style="display: none;">
            <template x-for="type in ['Comparative Analysis', 'Growth Strategy', 'Financial Audit', 'SWOT Analysis']">
                <button @click="analysisType = type; open = false" class="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50" x-text="type"></button>
            </template>
        </div>
    </div>
</div>
