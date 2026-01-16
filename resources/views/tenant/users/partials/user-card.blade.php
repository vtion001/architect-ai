{{-- Users Management - User Card --}}
<div class="bg-card border border-border rounded-[32px] p-6 hover:border-primary/30 transition-all group flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="relative">
            <div class="w-14 h-14 rounded-2xl bg-muted border border-border flex items-center justify-center text-foreground font-black text-xl overflow-hidden shadow-sm">
                {{ substr($user->email, 0, 1) }}
            </div>
            @if($user->last_login_at?->isAfter(now()->subDay()))
                <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 border-4 border-card rounded-full animate-pulse"></div>
            @endif
        </div>
        <div>
            <h4 class="font-black text-foreground uppercase tracking-tight">{{ $user->email }}</h4>
            <div class="flex items-center gap-3 mt-1">
                @foreach($user->roles as $role)
                    <span class="text-[9px] font-black uppercase text-primary tracking-widest">{{ $role->name }}</span>
                @endforeach
                <span class="text-muted-foreground/30">•</span>
                <span class="text-[9px] font-bold text-muted-foreground uppercase">MFA: {{ $user->mfa_enabled ? 'Enabled' : 'Disabled' }}</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-6">
        <div class="text-right hidden md:block">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-0.5">Last Grid Sync</p>
            <p class="text-[10px] font-bold text-foreground italic">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</p>
        </div>
        <button class="w-10 h-10 rounded-xl bg-muted/50 border border-border flex items-center justify-center text-muted-foreground hover:bg-white hover:text-black transition-all">
            <i data-lucide="more-horizontal" class="w-4 h-4"></i>
        </button>
    </div>
</div>
