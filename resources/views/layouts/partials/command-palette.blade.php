{{-- Command Palette Overlay --}}
{{--
    Expects parent x-data context with:
    - showCommandPalette, searchQuery, commands, searchResults, isSearching
    - filteredCommands (computed), fetchResults()
--}}
<div x-show="showCommandPalette" x-cloak class="fixed inset-0 z-[200] flex items-start justify-center pt-[15vh] px-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-200">
    <div @click.away="showCommandPalette = false" class="bg-card w-full max-w-xl rounded-2xl shadow-2xl border border-border overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-4 border-b border-border flex items-center gap-3 bg-muted/20">
            <template x-if="!isSearching">
                <i data-lucide="search" class="w-5 h-5 text-primary"></i>
            </template>
            <template x-if="isSearching">
                <i data-lucide="loader-2" class="w-5 h-5 text-primary animate-spin"></i>
            </template>
            <input type="text" x-model="searchQuery" x-ref="commandInput" autofocus
                   placeholder="Search across your grid nodes..." 
                   class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium outline-none">
            <span class="text-[9px] font-black border border-border px-1.5 py-0.5 rounded uppercase text-muted-foreground">ESC</span>
        </div>

        <div class="max-h-96 overflow-y-auto p-2 custom-scrollbar">
            <template x-for="cmd in filteredCommands" :key="cmd.url + cmd.title">
                <a :href="cmd.url" class="flex items-center justify-between p-3 rounded-xl hover:bg-primary/5 group transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-muted flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                            <i :data-lucide="cmd.icon" class="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-foreground" x-text="cmd.title"></p>
                            <p x-show="cmd.type" class="text-[8px] font-black uppercase text-slate-500 tracking-widest" x-text="cmd.type"></p>
                        </div>
                    </div>
                    <span class="mono text-[9px] text-slate-500 font-black uppercase tracking-widest" x-text="cmd.shortcut || cmd.metadata"></span>
                </a>
            </template>
            <div x-show="filteredCommands.length === 0" class="py-12 text-center text-muted-foreground italic text-xs">
                No protocols found matching that query.
            </div>
        </div>

        <div class="p-3 bg-muted/30 border-t border-border flex items-center justify-between px-6">
            <div class="flex gap-4">
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                    <i data-lucide="corner-down-left" class="w-3 h-3"></i> Select
                </span>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                    <i data-lucide="arrow-up-down" class="w-3 h-3"></i> Navigate
                </span>
            </div>
            <span class="text-[9px] font-black text-primary uppercase tracking-[0.2em]">ArchitGrid OS</span>
        </div>
    </div>
</div>
