@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md animate-in fade-in zoom-in-95 duration-500">
    <div class="bg-card border border-border rounded-3xl shadow-2xl p-10 space-y-8 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
        
        <div class="text-center">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Claim Identity</h2>
            <p class="text-sm text-muted-foreground italic">You've been invited to join <strong>{{ $invitation->tenant->name }}</strong>.</p>
        </div>

        <form action="{{ url('/auth/join/' . $invitation->token) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Authorized Email</label>
                <input type="email" value="{{ $invitation->email }}" disabled
                       class="w-full h-14 bg-muted/50 border border-border rounded-2xl px-5 text-sm font-bold opacity-60">
            </div>

            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Define Passphrase</label>
                    <input type="password" name="password" required placeholder="Minimum 12 characters"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Confirm Passphrase</label>
                    <input type="password" name="password_confirmation" required placeholder="Verify security key"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
            </div>

            <button type="submit" class="w-full h-16 bg-primary text-primary-foreground font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-3">
                <i data-lucide="key" class="w-5 h-5 fill-current"></i>
                ACTIVATE ACCOUNT
            </button>
        </form>

        <div class="pt-4 text-center">
            <p class="text-[10px] text-muted-foreground uppercase tracking-widest font-bold">
                Assigned Access: <span class="text-primary">{{ $invitation->role->name }}</span>
            </p>
        </div>
    </div>
</div>
@endsection
