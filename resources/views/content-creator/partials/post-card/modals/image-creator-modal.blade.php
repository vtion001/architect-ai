{{-- Banana Pro Image Creator Modal --}}
{{-- 
    Expects parent x-data context with:
    showImageCreatorModal, imageFormat, imagePrompt, posterText, 
    selectedAssetUrl, selectedBrandId, brands, mediaAssets, isLoadingAssets,
    isGenerating, generateAdvancedImage()
--}}
<template x-teleport="body">
    <div x-show="showImageCreatorModal" 
         x-cloak
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" 
         x-transition>
        <div @click.away="showImageCreatorModal = false" class="bg-card w-full max-w-2xl rounded-3xl shadow-2xl border border-border overflow-hidden animate-in zoom-in-95 duration-200">
            {{-- Header --}}
            <div class="p-6 border-b border-border bg-gradient-to-r from-purple-500/10 to-pink-500/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg transform rotate-3">
                            <i data-lucide="sparkles" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black tracking-tight text-foreground uppercase italic">Banana Pro Studio</h3>
                            <p class="text-[10px] text-muted-foreground uppercase tracking-[0.2em] font-bold opacity-70">Industrial Intelligence // Visual Synth</p>
                        </div>
                    </div>
                    <button @click="showImageCreatorModal = false" class="w-10 h-10 rounded-xl hover:bg-muted flex items-center justify-center transition-all">
                        <i data-lucide="x" class="w-5 h-5 text-muted-foreground"></i>
                    </button>
                </div>
            </div>

            {{-- Format Tabs --}}
            <div class="flex p-1 bg-muted/30 border-b border-border">
                <button @click="imageFormat = 'realistic'" 
                        :class="imageFormat === 'realistic' ? 'bg-card text-primary shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                        class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-[0.1em] rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    Realistic
                </button>
                <button @click="imageFormat = 'poster'" 
                        :class="imageFormat === 'poster' ? 'bg-card text-primary shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                        class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-[0.1em] rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="layout" class="w-4 h-4"></i>
                    Brand Poster
                </button>
                <button @click="imageFormat = 'asset-reference'" 
                        :class="imageFormat === 'asset-reference' ? 'bg-card text-primary shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                        class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-[0.1em] rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    Reference
                </button>
            </div>

            {{-- Content Area --}}
            <div class="p-8 space-y-6 max-h-[65vh] overflow-y-auto custom-scrollbar">
                {{-- Prompt Input (Always Visible) --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2 italic">
                        <i data-lucide="terminal" class="w-3 h-3"></i>
                        Neural Prompt Sequence
                    </label>
                    <textarea x-model="imagePrompt" 
                              rows="3" 
                              class="w-full bg-muted/20 border border-border rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary/50 outline-none resize-none transition-all"
                              placeholder="Describe the image you want to generate..."></textarea>
                </div>

                {{-- Realistic Photo Mode --}}
                <div x-show="imageFormat === 'realistic'" x-transition class="bg-blue-500/5 rounded-2xl border border-blue-500/10 p-6">
                    <div class="flex items-center gap-3 text-blue-500 mb-3">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Photographic Protocol</span>
                    </div>
                    <p class="text-xs text-muted-foreground leading-relaxed italic">
                        Generates authentic, high-fidelity photos using neural documentary styles. Perfect for realistic lifestyle, high-end product architecture, and editorial-grade content.
                    </p>
                </div>

                {{-- Poster Mode --}}
                <div x-show="imageFormat === 'poster'" x-transition class="space-y-6">
                    <div class="bg-purple-500/5 rounded-2xl border border-purple-500/10 p-6">
                        <div class="flex items-center gap-3 text-purple-500 mb-3">
                            <i data-lucide="palette" class="w-5 h-5"></i>
                            <span class="text-xs font-black uppercase tracking-widest">Brand Layout Engine</span>
                        </div>
                        <p class="text-xs text-muted-foreground leading-relaxed italic">
                            Synthesizes a high-impact marketing graphic by injecting your brand's color palette and typography directly into the neural generation process.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Overlay Text --}}
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic">
                                Headline Overlay
                            </label>
                            <input x-model="posterText" 
                                   type="text" 
                                   maxlength="80"
                                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none"
                                   placeholder="Catchy headline...">
                            <div class="flex justify-between items-center px-1">
                                <span class="text-[9px] text-muted-foreground italic">MAX 80 CHARS</span>
                                <span class="text-[9px] font-bold" :class="posterText.length > 70 ? 'text-amber-500' : 'text-slate-400'" x-text="posterText.length + '/80'"></span>
                            </div>
                        </div>

                        {{-- Brand Selector --}}
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic">
                                Identity Context
                            </label>
                            <div class="relative">
                                <select x-model="selectedBrandId" 
                                        class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none appearance-none cursor-pointer">
                                    <option value="">No Identity Context</option>
                                    <template x-for="brand in brands" :key="brand.id">
                                        <option :value="brand.id" x-text="brand.name"></option>
                                    </template>
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Asset Reference Mode --}}
                <div x-show="imageFormat === 'asset-reference'" x-transition class="space-y-6">
                    <div class="bg-amber-500/5 rounded-2xl border border-amber-500/10 p-6">
                        <div class="flex items-center gap-3 text-amber-500 mb-3">
                            <i data-lucide="fingerprint" class="w-5 h-5"></i>
                            <span class="text-xs font-black uppercase tracking-widest">Visual Reference Protocol</span>
                        </div>
                        <p class="text-xs text-muted-foreground leading-relaxed italic">
                            Sync with existing media assets to mirror composition, mood, and lighting. The AI will treat the selected image as a "neural anchor" for the new generation.
                        </p>
                    </div>

                    {{-- Asset Grid --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic">
                            Select Neural Anchor
                        </label>
                        
                        <div x-show="isLoadingAssets" class="flex items-center justify-center py-12">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-full border-2 border-primary/20 border-t-primary animate-spin"></div>
                                <i data-lucide="database" class="w-4 h-4 text-primary absolute inset-0 m-auto"></i>
                            </div>
                        </div>

                        <div x-show="!isLoadingAssets && mediaAssets.length > 0" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                            <template x-for="asset in mediaAssets" :key="asset.id">
                                <button @click="selectedAssetUrl = asset.url" 
                                        class="relative aspect-square rounded-xl overflow-hidden border border-border transition-all group hover:scale-[1.05] active:scale-[0.95]"
                                        :class="selectedAssetUrl === asset.url ? 'ring-4 ring-primary ring-offset-4' : 'hover:border-primary/50'">
                                    <img :src="asset.url" class="w-full h-full object-cover" :alt="asset.name">
                                    <div x-show="selectedAssetUrl === asset.url" class="absolute inset-0 bg-primary/20 flex items-center justify-center">
                                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center shadow-lg">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <div x-show="!isLoadingAssets && mediaAssets.length === 0" class="text-center py-12 border-2 border-dashed border-border rounded-2xl bg-muted/10 opacity-50 italic">
                            <i data-lucide="image-off" class="w-10 h-10 mx-auto mb-3 text-slate-400"></i>
                            <p class="text-xs font-bold uppercase tracking-widest">No assets found in registry</p>
                        </div>

                        {{-- Selected Asset Preview --}}
                        <div x-show="selectedAssetUrl" class="flex items-center gap-4 p-4 bg-primary/5 rounded-2xl border border-primary/20 shadow-sm animate-in slide-in-from-bottom-2">
                            <div class="w-16 h-16 rounded-xl overflow-hidden shadow-md shrink-0">
                                <img :src="selectedAssetUrl" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-black uppercase text-primary tracking-widest mb-1">Anchor Locked</p>
                                <p class="text-xs text-muted-foreground italic font-medium truncate max-w-[200px]">Visual style will be synchronized from this asset.</p>
                            </div>
                            <button @click="selectedAssetUrl = null" class="w-8 h-8 rounded-lg hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-6 border-t border-border bg-muted/30 flex items-center justify-between">
                <button @click="showImageCreatorModal = false" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-foreground transition-all">
                    Abort Protocol
                </button>
                <button @click="generateAdvancedImage()" 
                        :disabled="isGenerating || (imageFormat === 'asset-reference' && !selectedAssetUrl)"
                        class="px-10 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-purple-500/20 hover:shadow-purple-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-3">
                    <template x-if="!isGenerating">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </template>
                    <template x-if="isGenerating">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                    <span x-text="isGenerating ? 'Synthesizing...' : 'Initiate Synth'"></span>
                </button>
            </div>
        </div>
    </div>
</template>
