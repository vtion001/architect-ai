{{-- Users Management - Page Header --}}
<div class="mb-12 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Identity Management</h1>
        <p class="text-muted-foreground font-medium italic">Oversee the authorized personnel and security health of your agency grid.</p>
    </div>
    <button @click="showInviteModal = true" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        Invite New Identity
    </button>
</div>
