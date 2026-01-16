{{-- Connect Channels Modal --}}
@php
    $platformConfig = [
        'facebook' => 'blue-600', 
        'instagram' => 'pink-600', 
        'linkedin' => 'blue-800', 
        'twitter' => 'sky-400'
    ];
@endphp

<div x-show="showConnectModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div @click.away="showConnectModal = false" class="bg-card w-full max-w-lg rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
        
        <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Authorize Nodes</h2>
        <p class="text-sm text-muted-foreground mb-8 italic">Link your agency's social identities to the ArchitGrid.</p>

        <div class="space-y-4">
            @foreach($platformConfig as $plat => $c)
                <div class="flex items-center justify-between p-5 rounded-[32px] border border-border bg-muted/5 group hover:border-{{ $c }}/30 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $c }}/10 flex items-center justify-center text-{{ $c }} border border-{{ $c }}/20">
                            <i data-lucide="{{ $plat }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-sm uppercase tracking-tight text-foreground">{{ ucfirst($plat) }}</h4>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest" x-text="connectedAccounts.{{ $plat }} ? 'Identity Verified' : 'Awaiting Connection'"></p>
                        </div>
                    </div>
                    <button @click="connectAccount('{{ $plat }}')" 
                            class="px-6 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest transition-all"
                            :class="connectedAccounts.{{ $plat }} ? 'bg-green-500/10 text-green-500 hover:bg-green-500 hover:text-white' : 'bg-primary text-primary-foreground hover:bg-primary/90'">
                        <span x-text="connectedAccounts.{{ $plat }} ? 'Sync' : 'Link'"></span>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>
