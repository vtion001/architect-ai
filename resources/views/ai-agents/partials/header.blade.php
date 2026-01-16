{{-- AI Agents Page Header --}}
<div class="mb-12 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">AI Agent Protocol</h1>
        <p class="text-muted-foreground font-medium italic">Deploy specialized autonomous agents grounded in your knowledge base.</p>
    </div>
    <button @click="showCreateModal = true; resetNewAgent()" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 flex items-center gap-2 hover:scale-[1.02] transition-all">
        <i data-lucide="bot" class="w-4 h-4"></i>
        Deploy New Agent
    </button>
</div>
