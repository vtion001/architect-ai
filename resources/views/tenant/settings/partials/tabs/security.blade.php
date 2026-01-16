{{-- Security Hub Tab --}}
@props(['user'])

<div x-show="activeTab === 'security'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
    <h2 class="text-2xl font-black uppercase tracking-tighter mb-8">Security Protocols</h2>
    
    <div class="space-y-8">
        {{-- MFA Protocol --}}
        <div class="p-8 rounded-[32px] border border-border flex items-center justify-between relative overflow-hidden group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                    <i data-lucide="shield-check" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black uppercase tracking-tight">Multi-Factor Authentication</h3>
                    <p class="text-xs text-muted-foreground font-medium italic">Requirement level: High. Protect your agency identity.</p>
                </div>
            </div>
            
            @if($user->mfa_enabled)
                <div class="flex items-center gap-4">
                    <span class="text-[9px] font-black text-green-500 uppercase tracking-widest">Protocol Active</span>
                    <form action="{{ route('settings.mfa.disable') }}" method="POST">
                        @csrf
                        <button class="h-10 px-6 rounded-xl border border-red-100 bg-red-50 text-red-600 font-black uppercase text-[9px] tracking-widest hover:bg-red-600 hover:text-white transition-all">Deactivate</button>
                    </form>
                </div>
            @else
                <a href="{{ route('mfa.setup') }}" class="h-12 px-8 bg-purple-600 text-white rounded-xl font-black uppercase text-[9px] tracking-widest shadow-lg shadow-purple-900/20 hover:scale-[1.02] transition-all">Initialize MFA</a>
            @endif
        </div>

        {{-- Session Registry --}}
        <div class="p-8 rounded-[32px] border border-border">
            <div class="flex items-center gap-3 mb-6">
                <i data-lucide="monitor" class="w-5 h-5 text-slate-400"></i>
                <h3 class="text-sm font-black uppercase tracking-widest">Active Identity Sessions</h3>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 rounded-2xl bg-muted/20 border border-border">
                    <div class="flex items-center gap-4">
                        <i data-lucide="chrome" class="w-5 h-5 text-blue-500"></i>
                        <div>
                            <p class="text-xs font-bold text-foreground">Chrome on MacOS (This Session)</p>
                            <p class="text-[9px] text-slate-500 font-mono">{{ request()->ip() }}</p>
                        </div>
                    </div>
                    <span class="text-[8px] font-black text-primary uppercase tracking-widest">Current</span>
                </div>
            </div>
        </div>
    </div>
</div>
