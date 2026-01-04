@extends('layouts.app')

@section('content')
<div class="p-8 max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('policies.index') }}" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Policies
        </a>
        <h1 class="text-3xl font-bold text-foreground">Architect New Policy</h1>
        <p class="text-muted-foreground font-medium">Define a dynamic access rule for your organization.</p>
    </div>

    <div class="bg-card border border-border rounded-3xl shadow-xl overflow-hidden shadow-primary/5">
        <form action="{{ route('policies.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Policy Name</label>
                <input type="text" name="name" required placeholder="e.g., Restricted Content Editing"
                       class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Effect</label>
                    <select name="effect" required class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="allow">Allow</option>
                        <option value="deny">Deny</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Priority (Higher First)</label>
                    <input type="number" name="priority" value="0" required
                           class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 flex items-center justify-between">
                    Conditions (JSON Logic)
                    <span class="text-primary font-bold">ABAC Document</span>
                </label>
                <textarea name="conditions" rows="8" required 
                          class="w-full bg-muted/20 border border-border rounded-xl p-4 text-xs font-mono focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                          placeholder='{
  "all": [
    { "attribute": "user.role", "operator": "equals", "value": "content_editor" },
    { "attribute": "action", "operator": "equals", "value": "content.publish" }
  ]
}'></textarea>
                <p class="text-[10px] text-muted-foreground italic leading-tight mt-2 px-1">
                    Attributes: <code>user.id</code>, <code>user.role</code>, <code>action</code>, <code>resource.owner_id</code>, <code>resource.status</code>.<br>
                    Operators: <code>equals</code>, <code>not_equals</code>, <code>in</code>, <code>contains</code>.
                </p>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full h-14 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.2em] shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3 text-xs">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                    Activate Policy
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
