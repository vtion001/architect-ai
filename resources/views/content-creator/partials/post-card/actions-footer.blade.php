{{-- Post Card Actions Footer - Edit, Redo, Publish Buttons --}}
{{-- 
    Expects parent x-data context with:
    isEditing, isRegenerating, isPublishing,
    toggleEdit(), regenerateText(), openPublishModal()
--}}
<div class="px-4 py-3 border-t border-border bg-muted/5 flex items-center justify-end gap-3">
    {{-- Edit Button --}}
    <button @click="toggleEdit()" class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-primary hover:border-primary/30 transition-all text-xs font-bold uppercase tracking-wider" title="Edit Content">
        <i x-show="!isEditing" data-lucide="pencil" class="w-4 h-4"></i>
        <i x-show="isEditing" data-lucide="check" class="w-4 h-4"></i>
        <span x-text="isEditing ? 'Done' : 'Edit'"></span>
    </button>
    
    {{-- Redo Button --}}
    <button @click="regenerateText()" :disabled="isRegenerating" class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all text-xs font-bold uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed" title="Regenerate Text">
        <i x-show="!isRegenerating" data-lucide="refresh-cw" class="w-4 h-4"></i>
        <i x-show="isRegenerating" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
        <span x-text="isRegenerating ? 'Redoing...' : 'Redo'" class="hidden sm:inline"></span>
    </button>
    
    {{-- Publish Button --}}
    <button @click="openPublishModal()" :disabled="isPublishing" class="flex items-center justify-center gap-2 py-2 px-4 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-all text-xs font-bold uppercase tracking-wider shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
        <i x-show="!isPublishing" data-lucide="send" class="w-4 h-4"></i>
        <i x-show="isPublishing" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
        <span x-text="isPublishing ? 'Publishing...' : 'Publish'"></span>
    </button>
</div>
