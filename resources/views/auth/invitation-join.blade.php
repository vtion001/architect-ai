@extends('layouts.auth')

@section('content')
<div class="w-full max-w-lg animate-in fade-in zoom-in-95 duration-500" x-data="{
    password: '',
    password_confirmation: '',
    isLoading: false,
    
    join() {
        if (this.password !== this.password_confirmation) {
            alert('Passphrases do not match.');
            return;
        }
        this.isLoading = true;
        this.$refs.form.submit();
    }
}">
    <div class="bg-card border border-border rounded-[40px] shadow-2xl relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>

        <div class="p-12 space-y-10 relative z-10">
            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-6 border border-primary/20">
                    <i data-lucide="shield-check" class="w-8 h-8 text-primary"></i>
                </div>
                <h2 class="text-3xl font-black uppercase tracking-tighter mb-2 text-foreground">Join the Grid</h2>
                <p class="text-sm text-muted-foreground italic font-medium">Provision your identity for <span class="text-primary font-bold">{{ $invitation->tenant->name }}</span>.</p>
            </div>

            <!-- Identity Specs -->
            <div class="p-6 rounded-3xl bg-muted/20 border border-border space-y-4">
                <div class="flex justify-between items-center px-1">
                    <span class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Authorized Email</span>
                    <span class="mono text-[9px] text-primary">{{ $invitation->email }}</span>
                </div>
                <div class="flex justify-between items-center px-1">
                    <span class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Assigned Role</span>
                    <span class="px-2 py-0.5 rounded bg-primary/10 text-primary text-[8px] font-black uppercase tracking-widest border border-primary/20">{{ $invitation->role->name }}</span>
                </div>
            </div>

            <form x-ref="form" action="{{ route('invitation.accept', $invitation->token) }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Establish Security Passphrase</label>
                        <div class="relative">
                            <i data-lucide="key" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="password" type="password" name="password" required placeholder="Min. 12 characters"
                                   class="w-full h-16 pl-11 bg-muted/20 border border-border rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                        @error('password') <p class="text-[9px] text-red-500 font-bold px-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Verify Passphrase</label>
                        <div class="relative">
                            <i data-lucide="shield-check" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="password_confirmation" type="password" name="password_confirmation" required placeholder="Re-enter for verification"
                                   class="w-full h-16 pl-11 bg-muted/20 border border-border rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="button" @click="join()" :disabled="isLoading"
                            class="w-full h-20 bg-primary text-primary-foreground rounded-3xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-4 disabled:opacity-50">
                        <template x-if="isLoading">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isLoading ? 'PROVISIONING...' : 'INITIALIZE IDENTITY'"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-muted/30 p-8 border-t border-border text-center">
            <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest leading-relaxed">
                Security Protocol Active. <br> 
                Identity data is hashed and isolated within the <span class="text-primary">{{ $invitation->tenant->slug }}</span> node.
            </p>
        </div>
    </div>
</div>
@endsection