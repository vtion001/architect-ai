{{-- Knowledge Hub Create Folder Modal --}}
<div x-show="showFolderModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div @click.away="!isSaving && (showFolderModal = false)" class="bg-card w-full max-w-md rounded-[32px] shadow-2xl border border-border p-8 animate-in zoom-in-95 duration-200">
        <h3 class="text-xl font-black uppercase tracking-tighter mb-6">Create Directory</h3>
        <input x-model="newFolderTitle" type="text" placeholder="Folder Name" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none mb-6">
        <div class="flex justify-end gap-3">
            <button @click="showFolderModal = false" class="px-6 py-3 rounded-xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-muted transition-all">Cancel</button>
            <button @click="createFolder" :disabled="isSaving" class="px-6 py-3 rounded-xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-widest hover:bg-primary/90 transition-all">Create</button>
        </div>
    </div>
</div>
