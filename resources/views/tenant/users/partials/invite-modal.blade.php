{{-- Users Management - Invite Modal --}}
<div x-show="showInviteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div @click.away="!isInviting && (showInviteModal = false)" class="bg-card w-full max-w-lg rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/10 rounded-full blur-3xl"></div>

        <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Provision Identity</h2>
        <p class="text-sm text-muted-foreground mb-10 italic">Authorize a new personnel node within your agency grid.</p>
        
        <form @submit.prevent="sendInvite" class="space-y-6 relative z-10">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 text-primary">Authorized Email</label>
                <input x-model="inviteEmail" type="email" required placeholder="personnel@agency.com"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Access Protocol (Role)</label>
                <select x-model="inviteRoleId" required class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    <option value="">Select Role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-6 flex flex-col gap-3">
                <button type="submit" :disabled="isInviting" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                    <template x-if="isInviting">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    </template>
                    <span x-text="isInviting ? 'PROVISIONING IDENTITY...' : 'INITIATE PROVISIONING'"></span>
                </button>
                <button type="button" @click="showInviteModal = false" :disabled="isInviting" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Abort</button>
            </div>
        </form>
    </div>
</div>
