@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showAddModal: false,
    showImpersonateModal: false,
    selectedSub: null,
    impersonateReason: 'Routine workspace management',
    name: '',
    slug: '',
    adminEmail: '',
    isProvisioning: false,
    isEntering: false,

    provisionSubAccount() {
        // ... (remains same)
    },

    enterSession() {
        if (!this.impersonateReason || this.impersonateReason.length < 10) {
            alert('A valid session goal (min 10 chars) is required.');
            return;
        }
        this.isEntering = true;
        fetch('{{ route('agency.impersonate') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ tenant_id: this.selectedSub.id, reason: this.impersonateReason })
        })
        .then(res => res.json())
        .then(data => {
            window.location.href = data.redirect;
        })
        .finally(() => { this.isEntering = false; });
    }
}">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Sub-Account Explorer</h1>
            <p class="text-muted-foreground font-medium italic">Monitor and manage nested client nodes within your agency grid.</p>
        </div>
        <button @click="showAddModal = true" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Provision New Node
        </button>
    </div>

    <!-- Sub-Account Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($subAccounts as $sub)
            <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
                <!-- Status & ID -->
                <div class="flex items-center justify-between mb-8">
                    <span class="px-2.5 py-1 rounded-lg bg-green-50 text-green-600 text-[9px] font-black uppercase tracking-widest border border-green-100">
                        {{ $sub->status }}
                    </span>
                    <span class="mono text-[8px] text-slate-400 uppercase tracking-widest">Node: {{ substr($sub->id, 0, 8) }}</span>
                </div>

                <!-- Identity -->
                <div class="space-y-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black text-xl border border-primary/20 group-hover:bg-primary group-hover:text-black transition-all">
                        {{ substr($sub->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-foreground truncate uppercase tracking-tight">{{ $sub->name }}</h3>
                        <p class="text-xs text-muted-foreground font-mono">/{{ $sub->slug }}</p>
                    </div>
                </div>

                <!-- Resource Telemetry -->
                <div class="grid grid-cols-2 gap-4 pt-6 border-t border-border/50">
                    <div>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Identity Count</p>
                        <p class="text-lg font-bold text-foreground">{{ $sub->users_count }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-primary uppercase tracking-widest mb-1">Treasury Balance</p>
                        <p class="text-lg font-black text-primary">{{ number_format($sub->token_balance) }}</p>
                    </div>
                </div>

                <!-- Actions Overlay -->
                <div class="mt-8 pt-6">
                    <button @click="selectedSub = @js($sub); showImpersonateModal = true" 
                            class="w-full py-3 rounded-xl border border-border bg-muted/5 font-black uppercase text-[10px] tracking-widest text-muted-foreground hover:bg-primary hover:text-white hover:border-primary transition-all">
                        Enter Workspace
                    </button>
                </div>
            </div>
        @empty
            <!-- ... empty state remains same -->
        @endforelse
    </div>

    <!-- Impersonation Protocol Modal -->
    <div x-show="showImpersonateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="!isEntering && (showImpersonateModal = false)" class="bg-card w-full max-w-md rounded-[40px] shadow-2xl border border-border p-10 text-center animate-in zoom-in-95 duration-200 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/10 rounded-full blur-3xl"></div>
            
            <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6 border border-primary/20">
                <i data-lucide="zap" class="w-10 h-10 text-primary"></i>
            </div>
            
            <h2 class="text-2xl font-black text-foreground mb-2 uppercase tracking-tighter">Enter Workspace</h2>
            <p class="text-muted-foreground text-sm mb-8 leading-relaxed italic">
                Initiating session protocol for <span class="text-primary font-bold" x-text="selectedSub?.name"></span>. 
                This action is audited by the Agency Master Identity.
            </p>

            <div class="space-y-6">
                <div class="space-y-2 text-left">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Session Goal</label>
                    <textarea x-model="impersonateReason" class="w-full h-24 bg-muted/20 border border-border rounded-2xl p-4 text-xs font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Required: Why do you need to enter this node?"></textarea>
                </div>
                
                <div class="flex flex-col gap-3">
                    <button @click="enterSession" :disabled="isEntering || impersonateReason.length < 10" class="w-full h-16 rounded-2xl bg-primary text-primary-foreground font-black uppercase tracking-widest text-xs shadow-lg shadow-primary/40 hover:bg-primary/90 transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                        <span x-show="!isEntering">Establish Connection</span>
                        <span x-show="isEntering" class="flex items-center gap-2">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                            Provisioning Access...
                        </span>
                    </button>
                    <button @click="showImpersonateModal = false" :disabled="isEntering" class="w-full h-14 rounded-2xl bg-muted text-muted-foreground font-bold uppercase tracking-widest text-[10px] hover:bg-muted/80 transition-all">Abort Protocol</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Provisioning Modal (Existing) -->
    <!-- ... (rest of the file remains same) -->
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
</div>
@endsection