<!-- Template Category Selection Grid -->
<div class="grid grid-cols-2 gap-4 pb-8" x-show="!showVariantModal">
    @foreach($templateCategories as $category)
    <button
        @click="
            selectedCategory = @js($category);
            template = '{{ $category['id'] }}';
            showVariantModal = true;
        "
        class="flex flex-col items-start p-6 rounded-[32px] border border-border bg-card/50 text-left transition-all hover:border-primary/40 group relative overflow-hidden min-h-[140px]"
        :class="template === '{{ $category['id'] }}' ? 'border-primary bg-primary/5 ring-1 ring-primary/20 shadow-lg shadow-primary/5' : ''"
    >
        <div class="w-full flex items-start justify-between relative z-20 mb-auto">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all border border-transparent group-hover:scale-110 shadow-sm"
                 style="background-color: {{ $category['color'] }}15; color: {{ $category['color'] }}; border-color: {{ $category['color'] }}30;">
                <i data-lucide="{{ $category['icon'] }}" class="w-6 h-6"></i>
            </div>
            <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-primary"></i>
            </div>
        </div>
        
        <div class="relative z-20 mt-4">
            <span class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 group-hover:text-foreground transition-colors block leading-tight mb-1">
                {{ $category['name'] }}
            </span>
            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                Configure Layout
            </span>
        </div>

        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.1] group-hover:scale-110 transition-all duration-500 pointer-events-none">
            <i data-lucide="{{ $category['icon'] }}" class="w-24 h-24" style="color: {{ $category['color'] }}"></i>
        </div>
    </button>
    @endforeach
</div>

<!-- Variant Selection Modal (Industrial Overlay) -->
<div x-show="showVariantModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" style="display: none;">
    <div class="bg-slate-900 border border-slate-800 rounded-[40px] shadow-2xl w-full max-w-5xl max-h-[85vh] flex flex-col overflow-hidden animate-in zoom-in-95 duration-200" @click.away="showVariantModal = false">
        
        <!-- Header Protocol -->
        <div class="p-8 border-b border-slate-800 flex items-center justify-between bg-slate-950/20">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center border" 
                     :style="{ backgroundColor: selectedCategory?.color + '15', color: selectedCategory?.color, borderColor: selectedCategory?.color + '30' }">
                    <i :data-lucide="selectedCategory?.icon" class="w-7 h-7"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black uppercase tracking-tighter text-white" x-text="selectedCategory?.name + ' Nodes'"></h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Select structural layout variant</p>
                </div>
            </div>
            <button @click="showVariantModal = false" class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center hover:bg-red-600 text-slate-400 hover:text-white transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Variants Grid -->
        <div class="flex-1 overflow-y-auto p-10 bg-white/5 custom-scrollbar">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <template x-for="variant in selectedCategory?.variants" :key="variant.id">
                    <div 
                        @click="templateVariant = variant.id; showVariantModal = false;"
                        class="group relative rounded-[32px] border border-slate-800 bg-slate-900 p-6 hover:border-primary transition-all cursor-pointer overflow-hidden flex flex-col h-full"
                    >
                        <!-- Blueprint Preview Image -->
                        <div class="mb-6 aspect-video w-full rounded-2xl overflow-hidden relative bg-slate-950 border border-slate-800 flex items-center justify-center group-hover:border-primary/50 transition-all">
                            <!-- Actual Thumbnail Image -->
                            <img :src="'/images/templates/' + selectedCategory?.id + '.png'" 
                                 class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-80 group-hover:scale-110 transition-all duration-700"
                                 :alt="variant.name">
                            
                            <!-- Tactical Overlays -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"></div>
                            <div class="absolute inset-0 grid-canvas pointer-events-none opacity-20"></div>
                            
                            <!-- Blueprint Icon (Centered) -->
                            <i :data-lucide="selectedCategory?.icon" 
                               class="relative z-10 w-12 h-12 opacity-40 group-hover:opacity-100 group-hover:scale-125 transition-all duration-500" 
                               :style="{ color: selectedCategory?.color }"></i>
                            
                            <!-- Visual Badge -->
                            <div class="absolute top-3 left-3 px-2 py-1 rounded-md bg-black/60 border border-white/10 backdrop-blur-md z-20">
                                <span class="mono text-[7px] font-black text-primary uppercase tracking-widest" x-text="'NODE_' + variant.id.toUpperCase().substring(0,8)"></span>
                            </div>

                            <!-- Scanline Effect -->
                            <div class="absolute inset-0 pointer-events-none bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%] opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        </div>

                        <div class="space-y-4 flex-1">
                            <h3 class="font-black text-white uppercase tracking-tight group-hover:text-primary transition-colors text-lg" x-text="variant.name"></h3>
                            <p class="text-[11px] text-slate-400 font-medium italic leading-relaxed" x-text="variant.description"></p>
                            
                            <div class="flex flex-wrap gap-2 pt-2">
                                <template x-for="tag in variant.tags" :key="tag">
                                    <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-500 text-[8px] font-black uppercase tracking-widest border border-slate-700" x-text="tag"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Hover Selection Pulse -->
                        <div class="mt-6 pt-6 border-t border-slate-800 flex justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-primary">Engage Layout</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer Registry -->
        <div class="p-6 border-t border-slate-800 bg-slate-950/40 flex justify-between items-center px-10">
            <p class="mono text-[8px] font-black uppercase tracking-[0.4em] text-slate-600 italic leading-none">ArchitGrid Protocol UI v1.0.4</p>
            <button @click="showVariantModal = false" class="h-12 px-8 rounded-xl bg-slate-800 text-slate-400 text-[9px] font-black uppercase tracking-widest hover:bg-slate-700 hover:text-white transition-all">Abort Selection</button>
        </div>
    </div>
</div>