@extends('layouts.app')

@section('content')
<div class="p-8 max-w-3xl mx-auto" x-data="{
    name: '',
    effect: 'allow',
    priority: 0,
    rules: [
        { attribute: 'user.role', operator: 'equals', value: '' }
    ],
    get jsonConditions() {
        return JSON.stringify({
            all: this.rules
        });
    },
    addRule() {
        this.rules.push({ attribute: 'user.role', operator: 'equals', value: '' });
    },
    removeRule(index) {
        this.rules.splice(index, 1);
        if (this.rules.length === 0) this.addRule();
    }
}">
    <div class="mb-8">
        <a href="{{ route('policies.index') }}" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Policies
        </a>
        <h1 class="text-3xl font-bold text-foreground uppercase tracking-tight">Architect New Policy</h1>
        <p class="text-muted-foreground font-medium italic">Construct dynamic ABAC rules for your agency grid.</p>
    </div>

    <div class="bg-card border border-border rounded-3xl shadow-2xl overflow-hidden relative">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
            <i data-lucide="shield-check" class="w-32 h-32 text-primary"></i>
        </div>

        <form action="{{ route('policies.store') }}" method="POST" class="p-10 space-y-10">
            @csrf
            
            <!-- Hidden Input for Controller -->
            <input type="hidden" name="conditions" :value="jsonConditions">

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Policy Identity</label>
                    <input type="text" name="name" x-model="name" required placeholder="e.g., Editor Publishing Restriction"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Effect</label>
                        <select name="effect" x-model="effect" required class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="allow">ALLOW</option>
                            <option value="deny">DENY</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Priority</label>
                        <input type="number" name="priority" x-model="priority" required
                               class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>
            </div>

            <!-- Rule Builder -->
            <div class="space-y-6">
                <div class="flex items-center justify-between px-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic">Condition Logic Grid</label>
                    <button type="button" @click="addRule()" class="text-[10px] font-black text-primary hover:underline flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i>
                        ADD REQUIREMENT
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(rule, index) in rules" :key="index">
                        <div class="group flex items-center gap-3 p-4 bg-muted/10 border border-border rounded-2xl hover:border-primary/30 transition-all animate-in fade-in slide-in-from-left-4 duration-300">
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- Attribute -->
                                <select x-model="rule.attribute" class="h-10 bg-card border border-border rounded-xl px-3 text-[11px] font-bold uppercase tracking-wider outline-none focus:ring-1 focus:ring-primary">
                                    <optgroup label="User Attributes">
                                        <option value="user.id">User ID</option>
                                        <option value="user.role">User Role</option>
                                        <option value="user.email">User Email</option>
                                    </optgroup>
                                    <optgroup label="Action Attributes">
                                        <option value="action">System Action</option>
                                    </optgroup>
                                    <optgroup label="Resource Attributes">
                                        <option value="resource.owner_id">Resource Owner</option>
                                        <option value="resource.status">Resource Status</option>
                                        <option value="resource.type">Resource Type</option>
                                    </optgroup>
                                </select>

                                <!-- Operator -->
                                <select x-model="rule.operator" class="h-10 bg-card border border-border rounded-xl px-3 text-[11px] font-bold uppercase tracking-wider outline-none focus:ring-1 focus:ring-primary">
                                    <option value="equals">Equals</option>
                                    <option value="not_equals">Does Not Equal</option>
                                    <option value="in">Is In (Comma Separated)</option>
                                    <option value="contains">Contains</option>
                                    <option value="starts_with">Starts With</option>
                                </select>

                                <!-- Value -->
                                <input type="text" x-model="rule.value" placeholder="Target Value..."
                                       class="h-10 bg-card border border-border rounded-xl px-4 text-xs font-bold outline-none focus:ring-1 focus:ring-primary">
                            </div>

                            <!-- Remove -->
                            <button type="button" @click="removeRule(index)" class="p-2 text-muted-foreground hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Preview -->
            <div class="bg-muted/5 border border-border/50 rounded-2xl p-6">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="code" class="w-4 h-4 text-muted-foreground"></i>
                    <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Live Protocol Preview (Read Only)</span>
                </div>
                <pre class="text-[10px] font-mono text-primary/70 overflow-x-auto whitespace-pre-wrap leading-relaxed" x-text="JSON.stringify({ Effect: effect, Priority: priority, Conditions: JSON.parse(jsonConditions) }, null, 2)"></pre>
            </div>

            <!-- Submit -->
            <div class="pt-6">
                <button type="submit" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-4 text-xs">
                    <i data-lucide="zap" class="w-6 h-6 fill-current"></i>
                    Deploy Access Protocol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection