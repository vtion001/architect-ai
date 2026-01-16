{{-- Knowledge Hub Index New Asset Modal --}}
<div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div @click.away="!isSaving && (showAddModal = false)" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 relative overflow-hidden">
        <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Index New Intelligence</h2>
        <p class="text-sm text-muted-foreground mb-10 italic">Store corporate data or research to ground AI generations.</p>
        
        <form @submit.prevent="saveAsset" class="space-y-6 relative z-10">
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Asset Title</label>
                    <input x-model="newAsset.title" type="text" required placeholder="e.g., Q1 Strategy Brief"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Asset Type</label>
                    <select x-model="newAsset.type" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="text">Raw Intelligence</option>
                        <option value="website">External Domain</option>
                        <option value="file">File Upload (PDF/MD/TXT)</option>
                    </select>
                </div>
            </div>

            {{-- File Upload Input (Conditional) --}}
            <div class="space-y-2" x-show="newAsset.type === 'file'">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Upload Document</label>
                <div class="border-2 border-dashed border-border rounded-2xl p-6 flex flex-col items-center justify-center hover:border-primary/50 transition-colors">
                    <i data-lucide="upload-cloud" class="w-8 h-8 text-primary mb-2"></i>
                    <input type="file" x-ref="fileInput" class="w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Intelligence Category</label>
                <input x-model="newAsset.category" type="text" placeholder="e.g., Client Alpha Context"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div class="space-y-2" x-show="newAsset.type !== 'file'">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Source Content</label>
                <textarea x-model="newAsset.content" rows="8" :required="newAsset.type !== 'file'" placeholder="Paste the intelligence data here..."
                          class="w-full bg-muted/20 border border-border rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
            </div>

            <div class="pt-6 flex flex-col gap-3">
                <button type="submit" :disabled="isSaving" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                    <template x-if="isSaving">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    </template>
                    <span x-text="isSaving ? 'INDEXING...' : 'INITIALIZE INDEXING'"></span>
                </button>
                <button type="button" @click="showAddModal = false" :disabled="isSaving" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Abort</button>
            </div>
        </form>
    </div>
</div>
