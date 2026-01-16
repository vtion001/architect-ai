{{-- Calendar Results View Sidebar Partial --}}
<div x-show="generatedCalendar" x-transition 
     class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden h-full">
    <div class="flex flex-col space-y-1.5 p-6 border-b border-border/50 bg-background/50 backdrop-blur-sm sticky top-0 z-10 flex-row items-center justify-between">
        <h3 class="text-xl font-bold leading-none tracking-tight flex items-center gap-2">
            <i data-lucide="calendar-check" class="w-5 h-5 text-primary"></i>
            Weekly Strategy
        </h3>
        <button @click="generatedCalendar = null" 
                class="text-xs font-bold text-muted-foreground hover:text-foreground">Close</button>
    </div>
    
    <div class="p-6 space-y-8 bg-muted/5 min-h-[600px] overflow-y-auto max-h-[800px] custom-scrollbar">
        
        {{-- Educational --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                <h4 class="text-xs font-black uppercase tracking-widest text-blue-500">Educational (Authority)</h4>
            </div>
            <template x-for="post in generatedCalendar.educational">
                <div class="bg-card border border-border rounded-xl p-4 space-y-3 hover:border-blue-500/30 transition-all">
                    <div>
                        <span class="text-[9px] font-black bg-blue-500/10 text-blue-500 px-2 py-0.5 rounded uppercase tracking-wider">Hook</span>
                        <p class="text-sm font-bold text-foreground mt-1" x-text="post.hook"></p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground leading-relaxed" x-text="post.caption"></p>
                    </div>
                    <div class="bg-muted/30 p-3 rounded-lg border border-border border-dashed flex gap-3 items-start">
                        <i data-lucide="image" class="w-4 h-4 text-muted-foreground mt-0.5"></i>
                        <p class="text-[10px] text-muted-foreground italic" x-text="post.visual_idea"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Showcase --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                <h4 class="text-xs font-black uppercase tracking-widest text-purple-500">Showcase (Expertise)</h4>
            </div>
            <template x-for="post in generatedCalendar.showcase">
                <div class="bg-card border border-border rounded-xl p-4 space-y-3 hover:border-purple-500/30 transition-all">
                    <div>
                        <span class="text-[9px] font-black bg-purple-500/10 text-purple-500 px-2 py-0.5 rounded uppercase tracking-wider">Hook</span>
                        <p class="text-sm font-bold text-foreground mt-1" x-text="post.hook"></p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground leading-relaxed" x-text="post.caption"></p>
                    </div>
                    <div class="bg-muted/30 p-3 rounded-lg border border-border border-dashed flex gap-3 items-start">
                        <i data-lucide="image" class="w-4 h-4 text-muted-foreground mt-0.5"></i>
                        <p class="text-[10px] text-muted-foreground italic" x-text="post.visual_idea"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Conversational --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                <h4 class="text-xs font-black uppercase tracking-widest text-green-500">Conversational (Community)</h4>
            </div>
            <template x-for="post in generatedCalendar.conversational">
                <div class="bg-card border border-border rounded-xl p-4 space-y-3 hover:border-green-500/30 transition-all">
                    <div>
                        <span class="text-[9px] font-black bg-green-500/10 text-green-500 px-2 py-0.5 rounded uppercase tracking-wider">Hook</span>
                        <p class="text-sm font-bold text-foreground mt-1" x-text="post.hook"></p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground leading-relaxed" x-text="post.caption"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Promotional --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                <h4 class="text-xs font-black uppercase tracking-widest text-amber-500">Promotional (Conversion)</h4>
            </div>
            <template x-for="post in generatedCalendar.promotional">
                <div class="bg-card border border-border rounded-xl p-4 space-y-3 hover:border-amber-500/30 transition-all">
                    <div>
                        <span class="text-[9px] font-black bg-amber-500/10 text-amber-500 px-2 py-0.5 rounded uppercase tracking-wider">Hook</span>
                        <p class="text-sm font-bold text-foreground mt-1" x-text="post.hook"></p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground leading-relaxed" x-text="post.caption"></p>
                    </div>
                    <div class="bg-muted/30 p-3 rounded-lg border border-border border-dashed flex gap-3 items-start">
                        <i data-lucide="image" class="w-4 h-4 text-muted-foreground mt-0.5"></i>
                        <p class="text-[10px] text-muted-foreground italic" x-text="post.visual_idea"></p>
                    </div>
                </div>
            </template>
        </div>

    </div>
</div>
