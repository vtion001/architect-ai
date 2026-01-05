@extends('layouts.app')

@section('content')
<div class="p-8 max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="mb-12">
        <a href="{{ route('policies.index') }}" class="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-primary transition-all flex items-center gap-2 mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Abort Architecture
        </a>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Protocol Architect</h1>
        <p class="text-muted-foreground font-medium italic">Define high-fidelity logic nodes for industrial access control.</p>
    </div>

    <div class="bg-card border border-border rounded-[40px] shadow-2xl relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
        
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

                <!-- Industrial Logic Builder -->
                <div class="p-8 rounded-[32px] bg-muted/10 border border-border space-y-8">
                    <div class="flex items-center gap-3">
                        <i data-lucide="cpu" class="w-5 h-5 text-primary"></i>
                        <h3 class="text-xs font-black uppercase tracking-widest">Logic Node Configuration</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-[8px] font-black uppercase text-slate-500 tracking-widest px-1">Attribute</label>
                            <select x-model="attribute" class="w-full h-12 bg-card border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
                                <option value="user.role">Identity Role</option>
                                <option value="user.email">Identity Email</option>
                                <option value="action">Protocol Action</option>
                                <option value="resource.type">Resource Module</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[8px] font-black uppercase text-slate-500 tracking-widest px-1">Operator</label>
                            <select x-model="operator" class="w-full h-12 bg-card border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
                                <option value="equals">IS EQUAL TO</option>
                                <option value="not_equals">NOT EQUAL TO</option>
                                <option value="in">IS IN ARRAY</option>
                                <option value="contains">CONTAINS STRING</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[8px] font-black uppercase text-slate-500 tracking-widest px-1">Evaluation Value</label>
                            <input type="text" x-model="value" placeholder="Value..."
                                   class="w-full h-12 bg-card border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
                        </div>
                    </div>

                    <!-- Computed JSON Output (Hidden) -->
                    <input type="hidden" name="conditions" :value="JSON.stringify({ attribute: attribute, operator: operator, value: value })">

                    <div class="p-4 rounded-xl bg-black/20 border border-white/5 mono text-[9px] text-slate-500 overflow-hidden truncate">
                        PROTOCOL_DATA: {"attribute":"<span class="text-primary" x-text="attribute"></span>","operator":"<span class="text-primary" x-text="operator"></span>","value":"<span class="text-primary" x-text="value"></span>"}
                    </div>
                </div>
            </div>

            <div class="pt-6 flex flex-col gap-3">
                <button type="submit" class="w-full h-20 bg-primary text-primary-foreground rounded-3xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-white hover:text-black transition-all flex items-center justify-center gap-4 group">
                    <i data-lucide="shield-check" class="w-5 h-5 fill-current"></i>
                    <span>Initialize Security Protocol</span>
                </button>
            </div>
        </form>

        <!-- Technical Mark -->
        <div class="p-8 border-t border-border bg-muted/30 flex justify-between items-center opacity-30 mono text-[8px] font-black uppercase tracking-[0.4em]">
            <span>ArchitGrid Security Architect v1.0.4</span>
            <span>Policy Mode: Active ABAC</span>
        </div>
    </div>
</div>
@endsection
