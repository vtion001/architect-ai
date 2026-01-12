@extends('layouts.auth')

@section('content')
<div class="w-full max-w-lg animate-in fade-in zoom-in-95 duration-500" x-data="{
    password: '',
    password_confirmation: '',
    isLoading: false,
    
    join() {
        if (this.password !== this.password_confirmation) {
            alert('Passwords do not match.');
            return;
        }
        this.isLoading = true;
        this.$refs.form.submit();
    }
}">
    <div class="glass-card rounded-[32px] shadow-2xl overflow-hidden border border-white/10">
        <div class="p-8 md:p-12 space-y-10">
            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 flex items-center justify-center mx-auto mb-6 border border-cyan-500/20">
                    <i data-lucide="user-plus" class="w-8 h-8 text-cyan-400"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Accept Invitation</h2>
                <p class="text-sm text-slate-400">You've been invited to join <span class="text-cyan-400 font-bold">{{ $invitation->tenant->name }}</span>.</p>
            </div>

            <!-- Invitation Details -->
            <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Email Address</span>
                    <span class="text-sm font-medium text-white">{{ $invitation->email }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Your Role</span>
                    <span class="px-2 py-1 rounded bg-cyan-500/10 text-cyan-400 text-[10px] font-bold uppercase tracking-widest border border-cyan-500/20">{{ $invitation->role->name }}</span>
                </div>
            </div>

            <form x-ref="form" action="{{ route('invitation.accept', $invitation->token) }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Set Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="password" type="password" name="password" required placeholder="Min. 8 characters"
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                        @error('password') <p class="text-xs text-red-400 font-medium px-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Confirm Password</label>
                        <div class="relative">
                            <i data-lucide="check-circle" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="password_confirmation" type="password" name="password_confirmation" required placeholder="Re-enter password"
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <button type="button" @click="join()" :disabled="isLoading"
                        class="w-full h-14 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <template x-if="isLoading">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                    <span x-text="isLoading ? 'Joining...' : 'Accept & Join Workspace'"></span>
                </button>
            </form>
        </div>
        
        <div class="bg-white/5 p-8 text-center border-t border-white/10">
            <p class="text-xs text-slate-500">
                By joining, you agree to the workspace policies set by <span class="text-cyan-400">{{ $invitation->tenant->name }}</span>.
            </p>
        </div>
    </div>
</div>
@endsection