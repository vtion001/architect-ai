{{-- Sub-Accounts - Impersonate Modal --}}
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
