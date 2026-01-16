{{-- Policy Create - Form --}}
<form action="{{ route('policies.store') }}" method="POST" class="p-12 space-y-10 relative z-10" x-data="{ 
    effect: 'allow',
    priority: 10,
    attribute: 'user.role',
    operator: 'equals',
    value: 'Agency Admin'
}">
    @csrf
    
    <div class="space-y-8">
        <!-- Basic Node Identity -->
        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1">Protocol Identifier (Name)</label>
            <input type="text" name="name" required placeholder="e.g. Restrict Sub-Account Creation"
                   class="w-full h-16 bg-muted/20 border border-border rounded-2xl px-6 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Enforcement Effect</label>
                <select name="effect" x-model="effect" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    <option value="allow">ALLOW PROTOCOL</option>
                    <option value="deny">DENY PROTOCOL</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Priority Altitude</label>
                <input type="number" name="priority" x-model="priority" required
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
        </div>

        @include('tenant.policies.partials.logic-builder')
    </div>

    <div class="pt-6 flex flex-col gap-3">
        <button type="submit" class="w-full h-20 bg-primary text-primary-foreground rounded-3xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-white hover:text-black transition-all flex items-center justify-center gap-4 group">
            <i data-lucide="shield-check" class="w-5 h-5 fill-current"></i>
            <span>Initialize Security Protocol</span>
        </button>
    </div>
</form>
