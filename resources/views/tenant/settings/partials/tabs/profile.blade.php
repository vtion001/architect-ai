{{-- Profile Identity Tab --}}
@props(['user'])

<div x-show="activeTab === 'profile'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
    <h2 class="text-2xl font-black uppercase tracking-tighter mb-8">Personal Identity</h2>
    <form action="{{ route('settings.profile') }}" method="POST" class="space-y-8">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Authorized Email</label>
                <input type="email" name="email" value="{{ $user->email }}" required
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">New Passphrase</label>
                <input type="password" name="password" placeholder="••••••••••••"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Confirm Passphrase</label>
                <input type="password" name="password_confirmation" placeholder="••••••••••••"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
        </div>
        <div class="pt-6 border-t border-border">
            <button type="submit" class="h-14 px-10 bg-primary text-primary-foreground rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">Update Identity</button>
        </div>
    </form>
</div>
