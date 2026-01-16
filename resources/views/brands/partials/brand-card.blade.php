{{-- Individual Brand Card --}}
{{-- 
    @param $brand - The brand model instance
    Expects parent x-data context with: editBrand(), deleteBrand(), setDefault() methods
--}}
<div class="bg-card border border-border rounded-[32px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
    {{-- Default Badge --}}
    @if($brand->is_default)
        <div class="absolute top-6 right-6">
            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20">
                Default
            </span>
        </div>
    @else
        <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="setDefault('{{ $brand->id }}')" class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-muted text-muted-foreground hover:bg-primary/10 hover:text-primary transition-colors">
                Make Default
            </button>
        </div>
    @endif
    
    {{-- Brand Visuals --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-2xl border border-border flex items-center justify-center shadow-sm overflow-hidden bg-white">
            @if($brand->logo_url)
                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-full h-full object-contain p-2">
            @else
                <span class="text-2xl font-black text-slate-300">{{ substr($brand->name, 0, 1) }}</span>
            @endif
        </div>
        <div>
            <h3 class="text-xl font-black text-foreground tracking-tight">{{ $brand->name }}</h3>
            @if($brand->tagline)
                <p class="text-xs text-muted-foreground italic">{{ $brand->tagline }}</p>
            @endif
            <div class="flex gap-1 mt-1">
                <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['primary'] ?? '#000000' }}"></div>
                <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['secondary'] ?? '#ffffff' }}"></div>
                <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['accent'] ?? '#3b82f6' }}"></div>
            </div>
        </div>
    </div>

    {{-- Industry & Voice --}}
    <div class="bg-muted/30 rounded-2xl p-5 mb-6 flex-1 border border-border/50 space-y-3">
        @if($brand->industry)
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Industry</p>
                <p class="text-xs font-bold text-foreground">{{ $brand->industry }}</p>
            </div>
        @endif
        <div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Voice & Tone</p>
            <div class="flex flex-wrap gap-2 mt-1">
                <span class="px-2 py-1 bg-white border border-border rounded-md text-[10px] font-bold text-foreground">
                    {{ $brand->voice_profile['tone'] ?? 'Standard' }}
                </span>
                @if(!empty($brand->voice_profile['writing_style']))
                    <span class="px-2 py-1 bg-white border border-border rounded-md text-[10px] text-muted-foreground">
                        {{ $brand->voice_profile['writing_style'] }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 pt-6 border-t border-border/50">
        <button @click="editBrand(@js($brand))" class="flex-1 h-12 rounded-xl bg-card border border-border hover:bg-muted transition-all flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-widest">
            <i data-lucide="settings-2" class="w-4 h-4"></i>
            Configure
        </button>
        <button @click="deleteBrand('{{ $brand->id }}')" class="w-12 h-12 rounded-xl border border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    </div>
</div>
