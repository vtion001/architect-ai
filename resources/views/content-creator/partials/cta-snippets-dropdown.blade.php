{{-- CTA Snippets Dropdown Component --}}
{{-- Reusable dropdown for selecting/managing CTA snippets --}}
<div class="relative">
    <button @click="showCtaSnippets = !showCtaSnippets" type="button" 
            class="text-[10px] font-bold text-primary hover:underline flex items-center gap-1">
        <i data-lucide="list" class="w-3 h-3"></i>
        SNIPPETS
    </button>
    
    <div x-show="showCtaSnippets" 
         @click.away="showCtaSnippets = false; isManagingSnippets = false" 
         class="absolute right-0 mt-2 w-72 bg-card border border-border rounded-xl shadow-xl z-50 overflow-hidden flex flex-col" 
         x-transition x-cloak>
        
        {{-- Header --}}
        <div class="p-2 bg-muted/50 border-b border-border flex items-center justify-between px-3 shrink-0">
            <span class="text-[9px] font-black uppercase tracking-widest text-muted-foreground" 
                  x-text="isManagingSnippets ? 'Manage Snippets' : 'Quick Select'"></span>
            <button @click="isManagingSnippets = !isManagingSnippets" 
                    class="text-primary hover:text-primary/80 transition-colors" 
                    title="Manage Snippets">
                <i :class="isManagingSnippets ? 'fill-current' : ''" data-lucide="settings-2" class="w-3 h-3"></i>
            </button>
        </div>

        {{-- List Content --}}
        <div class="max-h-56 overflow-y-auto">
            {{-- Select Mode --}}
            <template x-if="!isManagingSnippets">
                <div>
                    <template x-for="snippet in cta_snippets">
                        <button @click="cta = snippet; showCtaSnippets = false" type="button" 
                                class="w-full text-left px-4 py-2.5 text-[11px] font-medium hover:bg-primary/10 hover:text-primary transition-colors border-b border-border/50 last:border-0" 
                                x-text="snippet"></button>
                    </template>
                    <div x-show="cta_snippets.length === 0" 
                         class="p-4 text-center text-[10px] text-muted-foreground italic">
                        No snippets found. Click settings to add one.
                    </div>
                </div>
            </template>
            
            {{-- Manage Mode --}}
            <template x-if="isManagingSnippets">
                <div class="p-1">
                    <template x-for="(snippet, index) in cta_snippets" :key="index">
                        <div class="flex items-center gap-1 px-2 py-1 border-b border-border/50 last:border-0 group">
                            <input type="text" x-model="cta_snippets[index]" 
                                   class="flex-1 bg-transparent text-[11px] font-medium border-none focus:ring-0 px-2 py-1.5 h-auto text-foreground rounded hover:bg-muted/50 focus:bg-muted transition-colors">
                            <button @click="removeSnippet(index)" 
                                    class="p-1.5 text-muted-foreground hover:text-red-500 hover:bg-red-500/10 rounded transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </template>
                    
                    {{-- Add New --}}
                    <div class="p-2 pt-3 mt-1 border-t border-border/50 bg-muted/20">
                        <div class="flex gap-2">
                            <input x-model="newSnippet" 
                                   @keydown.enter.prevent="addSnippet()" 
                                   type="text" 
                                   placeholder="Type & press Enter..." 
                                   class="flex-1 h-8 text-[11px] rounded-lg border border-border bg-background px-3 focus:ring-1 focus:ring-primary shadow-sm">
                            <button @click="addSnippet()" 
                                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-primary text-primary-foreground hover:opacity-90 shadow-sm transition-all active:scale-95">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
