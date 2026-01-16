{{-- Logo Upload Form Section --}}
{{-- 
    @param $mode - Either 'create' or 'edit'
    For create: uses logoPreview, handleLogoSelect
    For edit: uses editLogoPreview, handleEditLogoSelect, selectedBrand.logo_url
--}}
@props(['mode' => 'create'])

<div class="space-y-6 pt-6 border-t border-border/50">
    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
        <i data-lucide="image" class="w-3 h-3"></i> Brand Logo
    </h3>
    
    <div class="flex items-start gap-6">
        {{-- Logo Preview --}}
        <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-border bg-muted/20 flex items-center justify-center overflow-hidden shrink-0">
            @if($mode === 'create')
                <template x-if="logoPreview">
                    <img :src="logoPreview" class="w-full h-full object-contain p-2">
                </template>
                <template x-if="!logoPreview">
                    <div class="text-center">
                        <i data-lucide="image" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <span class="text-[9px] text-slate-400 uppercase font-bold">No Logo</span>
                    </div>
                </template>
            @else
                <template x-if="editLogoPreview">
                    <img :src="editLogoPreview" class="w-full h-full object-contain p-2">
                </template>
                <template x-if="!editLogoPreview && selectedBrand.logo_url">
                    <img :src="selectedBrand.logo_url" class="w-full h-full object-contain p-2">
                </template>
                <template x-if="!editLogoPreview && !selectedBrand.logo_url">
                    <div class="text-center">
                        <i data-lucide="image" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <span class="text-[9px] text-slate-400 uppercase font-bold">No Logo</span>
                    </div>
                </template>
            @endif
        </div>
        
        {{-- Upload Input --}}
        <div class="flex-1 space-y-3">
            <label class="block">
                <span class="text-[10px] font-black uppercase text-slate-500 italic">
                    {{ $mode === 'create' ? 'Upload Logo' : 'Upload New Logo' }}
                </span>
                <input type="file" 
                       @change="{{ $mode === 'create' ? 'handleLogoSelect' : 'handleEditLogoSelect' }}" 
                       accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml" 
                       class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
            </label>
            <p class="text-[10px] text-muted-foreground">
                {{ $mode === 'create' ? 'PNG, JPG, GIF, WebP or SVG. Max 5MB.' : 'Upload a new image to replace the current logo.' }}
            </p>
        </div>
    </div>
</div>
