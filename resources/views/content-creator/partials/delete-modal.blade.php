{{-- Delete Batch Modal Partial --}}
<div x-show="showDeleteModal"
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.away="!isDeleting && (showDeleteModal = false)" 
         class="bg-card w-full max-w-md rounded-2xl shadow-2xl border border-border p-8 text-center animate-in zoom-in-95 duration-300">
        
        <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
            <i data-lucide="alert-triangle" class="w-10 h-10 text-red-600"></i>
        </div>
        
        <h2 class="text-2xl font-bold text-foreground mb-2">Delete Entire Batch?</h2>
        <p class="text-muted-foreground mb-8 leading-relaxed">
            This action is <span class="text-red-600 font-bold uppercase tracking-tight">permanent</span>. It will remove the local record and attempt to delete all associated posts from your social media platforms.
        </p>
        
        <div class="flex flex-col gap-3">
            <button @click="deleteBatch()" 
                    :disabled="isDeleting"
                    class="w-full py-4 rounded-xl bg-red-600 text-white font-bold uppercase tracking-wider text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-200 disabled:opacity-50 flex items-center justify-center gap-2">
                <template x-if="isDeleting">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                </template>
                <span x-text="isDeleting ? 'Removing everything...' : 'Yes, Delete Batch'"></span>
            </button>
            <button @click="showDeleteModal = false" 
                    :disabled="isDeleting"
                    class="w-full py-4 rounded-xl bg-muted text-muted-foreground font-bold uppercase tracking-wider text-sm hover:bg-muted/80 transition-all disabled:opacity-50">
                Cancel
            </button>
        </div>
    </div>
</div>
