{{-- Document Builder - File Upload --}}
<div class="space-y-3">
    <label class="text-sm font-medium leading-none">Upload Document (PDF or Image)</label>
    <div class="border-2 border-dashed border-border rounded-xl p-4 transition-colors hover:border-primary/50 relative">
        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
        <div class="flex flex-col items-center justify-center py-4 text-center pointer-events-none">
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                <i data-lucide="upload" class="w-6 h-6 text-primary"></i>
            </div>
            <span class="text-sm font-medium">Click to upload or drag and drop</span>
            <span class="text-xs text-muted-foreground mt-1">PDF, JPEG, PNG, WEBP (max 10MB)</span>
        </div>
    </div>
</div>
