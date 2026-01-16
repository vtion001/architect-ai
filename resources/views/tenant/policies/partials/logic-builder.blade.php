{{-- Policy Create - Logic Builder --}}
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
