{{-- Settings Page Header --}}
@props(['tokenBalance'])

<div class="mb-12 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Grid Configuration</h1>
        <p class="text-muted-foreground font-medium italic">Adjust the identity, resources, and security protocols of your agency workspace.</p>
    </div>
    <div class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/20 flex items-center gap-3">
        <i data-lucide="coins" class="w-4 h-4 text-primary"></i>
        <span class="text-xs font-black uppercase text-primary tracking-widest">{{ number_format($tokenBalance) }} Tokens</span>
    </div>
</div>

@if(session('success'))
    <div class="mb-8 p-4 rounded-xl bg-green-50 border border-green-100 text-green-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-in slide-in-from-top-2">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
