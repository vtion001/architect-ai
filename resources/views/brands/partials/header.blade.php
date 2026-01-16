{{-- Brand Index Page Header --}}
<div class="mb-12 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Brand Identity Engine</h1>
        <p class="text-muted-foreground font-medium italic">Manage multiple brand personas, visual assets, and voice profiles.</p>
    </div>
    <button @click="showCreateModal = true; resetNewBrand()" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 flex items-center gap-2 hover:scale-[1.02] transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Create Brand Kit
    </button>
</div>
