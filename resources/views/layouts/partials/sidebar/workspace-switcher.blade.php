{{-- Workspace Switcher Dropdown --}}
<div class="px-4 py-4 border-b border-sidebar-border relative">
    <button @click="openSwitcher = !openSwitcher" class="w-full flex items-center justify-between p-2 rounded-xl bg-sidebar-accent/50 border border-sidebar-border hover:border-primary/30 transition-all group">
        <div class="flex items-center gap-2 overflow-hidden">
            <div class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i data-lucide="grid" class="w-3 h-3 text-primary"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-white truncate">{{ app(\App\Models\Tenant::class)->name }}</span>
        </div>
        <i data-lucide="chevrons-up-down" class="w-3 h-3 text-slate-500 group-hover:text-primary transition-colors"></i>
    </button>

    {{-- Switcher Dropdown --}}
    <div x-show="openSwitcher" @click.away="openSwitcher = false" x-cloak
         class="absolute left-4 right-4 mt-2 bg-sidebar border border-sidebar-border rounded-xl shadow-2xl z-50 py-2 animate-in slide-in-from-top-2 duration-200">
        <p class="px-4 py-2 text-[8px] font-black text-slate-500 uppercase tracking-widest">Authorized Nodes</p>
        
        @php
            $user = auth()->user();
            $baseTenant = \App\Models\Tenant::withoutGlobalScope('tenant')->find($user->tenant_id);
            $availableTenants = collect([$baseTenant]);
            if ($baseTenant->type === 'agency') {
                $availableTenants = $availableTenants->concat($baseTenant->subAccounts);
            }
        @endphp

        @foreach($availableTenants as $t)
            <a href="{{ route('tenant.switch', $t->id) }}" 
               class="flex items-center gap-3 px-4 py-2 hover:bg-sidebar-accent group transition-colors {{ app(\App\Models\Tenant::class)->id === $t->id ? 'bg-primary/5' : '' }}">
                <div class="w-2 h-2 rounded-full {{ app(\App\Models\Tenant::class)->id === $t->id ? 'bg-primary animate-pulse' : 'bg-slate-700 group-hover:bg-slate-500' }}"></div>
                <span class="text-[10px] font-bold {{ app(\App\Models\Tenant::class)->id === $t->id ? 'text-primary' : 'text-slate-400 group-hover:text-white' }} uppercase truncate">{{ $t->name }}</span>
            </a>
        @endforeach
    </div>
</div>
