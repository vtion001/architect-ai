{{-- Document Builder - Generate Actions --}}
<div class="flex gap-3">
    <button
        @click="generateReport"
        :disabled="isGenerating"
        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 flex-1"
    >
        <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="isGenerating"></i>
        <span x-show="isGenerating">Generating...</span>
        <i data-lucide="sparkles" class="w-4 h-4 mr-2" x-show="!isGenerating"></i>
        <span x-show="!isGenerating">Generate Report</span>
    </button>
</div>
