{{-- Sub-Accounts - Add Modal --}}
<div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div @click.away="!isProvisioning && (showAddModal = false)" class="bg-card w-full max-w-lg rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/10 rounded-full blur-3xl"></div>

        <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Provision Node</h2>
        <p class="text-sm text-muted-foreground mb-10 italic">Securely establish a nested workspace identity.</p>
        
        <form @submit.prevent="provisionSubAccount" class="space-y-6 relative z-10">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 text-primary">Workspace Name</label>
                <input x-model="name" type="text" required placeholder="e.g., Client Alpha"
                       @input="slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Workspace Slug</label>
                <input x-model="slug" type="text" required placeholder="client-alpha"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 mono text-[11px] font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Initial Admin Identity (Email)</label>
                <input x-model="adminEmail" type="email" required placeholder="admin@client.com"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div class="pt-6 flex flex-col gap-3">
                <button type="submit" :disabled="isProvisioning" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                    <template x-if="isProvisioning">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    </template>
                    <span x-text="isProvisioning ? 'PROVISIONING NODE...' : 'INITIATE PROVISIONING'"></span>
                </button>
                <button type="button" @click="showAddModal = false" :disabled="isProvisioning" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Abort</button>
            </div>
        </form>
    </div>
</div>
