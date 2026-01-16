{{-- Brand Kit Selector --}}
{{-- Expects parent x-data with: brands, selectedBrandId --}}
<div class="space-y-3" x-show="brands.length > 0">
    <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic px-1">Apply Brand Kit</label>
    <div class="relative">
        <i data-lucide="fingerprint" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-primary"></i>
        <select x-model="selectedBrandId" @change="fetchPreview" class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 pr-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none appearance-none cursor-pointer hover:bg-muted/30 transition-colors">
            <option value="">No Brand Applied</option>
            <template x-for="brand in brands" :key="brand.id">
                <option :value="brand.id" x-text="brand.name"></option>
            </template>
        </select>
        <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none"></i>
    </div>
</div>
