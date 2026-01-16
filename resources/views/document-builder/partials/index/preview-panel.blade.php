{{-- Document Builder - Preview Panel --}}
<div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-row items-center justify-between p-6">
        <div class="space-y-1.5">
            <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                <i data-lucide="eye" class="w-5 h-5"></i>
                Report Preview
            </h3>
            <p class="text-sm text-muted-foreground">Live preview of your generated report</p>
        </div>
        <div class="flex items-center gap-2">
            <button 
                @click="downloadPdf"
                :disabled="!htmlPreview"
                class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3 disabled:opacity-50"
            >
                <i data-lucide="download" class="w-3.5 h-3.5 mr-2"></i>
                PDF
            </button>
            <button 
                @click="handleFullView"
                :disabled="!htmlPreview"
                class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3 disabled:opacity-50"
            >
                <i data-lucide="maximize-2" class="w-3.5 h-3.5 mr-2"></i>
                Full View
            </button>
        </div>
    </div>
    <div class="p-6 pt-0">
        @include('document-builder.partials.index.preview-content')
    </div>
</div>
