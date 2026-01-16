{{-- Page Selection Modal - Facebook/Instagram Pages --}}
{{-- 
    Expects parent x-data context with:
    showPageModal, isFetchingPages, facebookPages, selectedFacebookPage,
    selectPage()
--}}
<div x-show="showPageModal" 
     x-cloak
     class="absolute inset-0 z-50 bg-background/80 backdrop-blur-sm flex items-center justify-center p-4 rounded-xl" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95">
    
    <div class="bg-card w-full max-w-[300px] max-h-[90%] rounded-2xl shadow-2xl border border-border p-5 flex flex-col overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="font-bold text-sm text-foreground">Select Page</h4>
                <p class="text-[10px] text-muted-foreground uppercase tracking-widest font-black">Choose destination</p>
            </div>
            <button @click="showPageModal = false" class="p-1.5 hover:bg-muted rounded-full transition-colors text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        
        {{-- Loading State --}}
        <div x-show="isFetchingPages" class="flex-1 py-12 flex flex-col items-center justify-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full border-2 border-primary/20 border-t-primary animate-spin"></div>
                <i data-lucide="facebook" class="w-4 h-4 text-primary absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground animate-pulse">Fetching Pages...</span>
        </div>

        {{-- Pages List --}}
        <div x-show="!isFetchingPages" class="flex-1 overflow-y-auto space-y-1.5 pr-1 custom-scrollbar">
            <template x-for="page in facebookPages" :key="page.id">
                <button @click="selectPage(page)" 
                        class="w-full text-left p-2.5 rounded-xl border border-border hover:bg-muted/50 flex items-center gap-3 transition-all group relative overflow-hidden" 
                        :class="{'bg-primary/5 border-primary/30 ring-1 ring-primary/10 shadow-sm': selectedFacebookPage && selectedFacebookPage.id === page.id}">
                    
                    {{-- Selected Indicator Glow --}}
                    <div x-show="selectedFacebookPage && selectedFacebookPage.id === page.id" class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent"></div>

                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white text-xs font-black shadow-md group-hover:scale-105 transition-transform shrink-0" x-text="page.name.charAt(0)"></div>
                    
                    <div class="flex-1 min-w-0 relative">
                        <p class="text-[11px] font-bold text-foreground truncate leading-tight" x-text="page.name"></p>
                        <p class="text-[9px] text-muted-foreground truncate uppercase tracking-tighter font-semibold" x-text="page.category || 'Facebook Page'"></p>
                    </div>

                    <div x-show="selectedFacebookPage && selectedFacebookPage.id === page.id" class="w-5 h-5 rounded-full bg-primary flex items-center justify-center shrink-0 shadow-sm">
                        <i data-lucide="check" class="w-3 h-3 text-white"></i>
                    </div>
                </button>
            </template>
            
            {{-- Empty State --}}
            <div x-show="facebookPages.length === 0" class="text-center py-8 px-4 bg-muted/20 rounded-xl border border-dashed border-border">
                <i data-lucide="alert-circle" class="w-8 h-8 text-muted-foreground/40 mx-auto mb-2"></i>
                <p class="text-[10px] font-bold text-muted-foreground leading-relaxed">
                    No pages found.<br>
                    <span class="font-medium opacity-70">Ensure permissions are granted in Social Planner.</span>
                </p>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-border flex justify-center">
            <button @click="showPageModal = false" class="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-primary transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>
