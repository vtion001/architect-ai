{{-- Settings Navigation Sidebar --}}
<div class="lg:col-span-3 space-y-2">
    <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
        <i data-lucide="user" class="w-4 h-4"></i>
        Personal Identity
    </button>
    
    @if(auth()->user()->tenant->type === 'agency')
    <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
        <i data-lucide="palette" class="w-4 h-4"></i>
        Visual DNA
    </button>
    <a href="{{ route('brands.index') }}" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all hover:bg-muted text-muted-foreground">
        <i data-lucide="fingerprint" class="w-4 h-4"></i>
        Brand Kits
    </a>
    <button @click="activeTab = 'billing'" :class="activeTab === 'billing' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
        <i data-lucide="credit-card" class="w-4 h-4"></i>
        Resource Treasury
    </button>
    @endif

    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
        <i data-lucide="shield-check" class="w-4 h-4"></i>
        Security Hub
    </button>
    <button @click="activeTab = 'api'" :class="activeTab === 'api' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
        <i data-lucide="terminal" class="w-4 h-4"></i>
        API Protocols
    </button>
</div>
