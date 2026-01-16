{{-- Color Palette Form Section --}}
{{-- 
    @param $modelPrefix - Either 'newBrand' or 'selectedBrand' for x-model binding
--}}
@props(['modelPrefix' => 'newBrand'])

<div class="space-y-6 pt-6 border-t border-border/50">
    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
        <i data-lucide="palette" class="w-3 h-3"></i> Color Palette
    </h3>
    
    <div class="grid grid-cols-3 gap-4">
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Primary</label>
            <div class="flex gap-2">
                <input x-model="{{ $modelPrefix }}.colors.primary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                <input x-model="{{ $modelPrefix }}.colors.primary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
            </div>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Secondary</label>
            <div class="flex gap-2">
                <input x-model="{{ $modelPrefix }}.colors.secondary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                <input x-model="{{ $modelPrefix }}.colors.secondary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
            </div>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Accent</label>
            <div class="flex gap-2">
                <input x-model="{{ $modelPrefix }}.colors.accent" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                <input x-model="{{ $modelPrefix }}.colors.accent" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
            </div>
        </div>
    </div>
</div>
