{{-- Post Card Content Body - Display/Edit Mode --}}
{{-- 
    Expects parent x-data context with: 
    isEditing, rawContent, htmlContent, showCopyToast, copyContent()
--}}
<div class="p-4 text-foreground relative group/content">
    {{-- Copy Button - appears on hover --}}
    <button @click="copyContent()" 
            x-show="!isEditing"
            class="absolute top-2 right-2 p-2 rounded-lg bg-white/80 border border-border shadow-sm opacity-0 group-hover/content:opacity-100 hover:bg-primary hover:text-white hover:border-primary transition-all z-10"
            title="Copy to clipboard">
        <i data-lucide="copy" class="w-4 h-4"></i>
    </button>
    
    {{-- Copy Success Toast --}}
    <div x-show="showCopyToast" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="absolute top-2 right-2 flex items-center gap-2 px-3 py-2 bg-green-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-lg z-20">
        <i data-lucide="check" class="w-4 h-4"></i>
        Copied!
    </div>
    
    {{-- Display Mode --}}
    <div x-show="!isEditing" class="prose prose-slate max-w-none dark:prose-invert prose-p:my-2 prose-headings:my-3 prose-ul:my-2 text-[15px] leading-relaxed" x-html="htmlContent">
    </div>
    
    {{-- Edit Mode --}}
    <textarea x-show="isEditing" x-model="rawContent" :class="window.__contentViewerConfig?.contentType === 'blog' ? 'min-h-[500px]' : 'h-64'" class="w-full p-3 bg-muted/20 border border-border rounded-lg text-sm focus:ring-1 focus:ring-primary outline-none resize-y font-mono" placeholder="Edit your post content..." x-cloak></textarea>
</div>
