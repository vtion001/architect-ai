<!-- Studio Tab (Ghost Recorder) -->
<div x-show="activeTab === 'studio'" class="space-y-4">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rrweb@latest/dist/rrweb.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/rrweb@latest/dist/rrweb.min.js"></script>

    <div x-show="!isGhostRecording" class="space-y-6 text-center py-4">
        <div class="space-y-2">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 shadow-inner">
                <i data-lucide="ghost" class="w-10 h-10 text-indigo-600"></i>
            </div>
            <h3 class="text-lg font-black text-foreground uppercase tracking-tight">Ghost Studio</h3>
            <p class="text-xs text-muted-foreground max-w-[240px] mx-auto leading-relaxed italic">
                Capture seamless DOM interactions. Record, edit, and replay your app demos with industrial precision.
            </p>
        </div>

        <button @click="startGhostRecording()" 
                class="w-full py-4 bg-indigo-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-500/30 flex items-center justify-center gap-3 active:scale-[0.98]">
            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.8)]"></div>
            Start Neural Recording
        </button>

        <div class="text-left border-t border-border pt-6 mt-2">
            <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                <i data-lucide="database" class="w-3 h-3"></i>
                Saved Demo Archives
            </h4>
            <div class="space-y-2.5">
                <template x-for="demo in ghostDemos" :key="demo.id">
                    <div class="flex items-center justify-between p-3 bg-card border border-border rounded-xl hover:border-indigo-500/40 transition-all group cursor-pointer hover:shadow-md" @click="playDemo(demo.id)">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-600 transition-colors">
                                <i data-lucide="play" class="w-4 h-4 text-indigo-600 group-hover:text-white transition-colors fill-current"></i>
                            </div>
                            <span x-text="demo.title" class="text-xs font-bold text-foreground truncate max-w-[180px]"></span>
                        </div>
                        <span x-text="formatDate(demo.created_at)" class="text-[9px] font-mono text-muted-foreground"></span>
                    </div>
                </template>
                <div x-show="ghostDemos.length === 0" class="text-center py-8 text-muted-foreground/50 text-[10px] italic border-2 border-dashed border-border rounded-xl bg-muted/5">
                    No sequences captured in archive.
                </div>
            </div>
        </div>
    </div>

    <!-- Recording State Overlay (Minimal) -->
    <div x-show="isGhostRecording" class="flex flex-col items-center justify-center py-12 space-y-6">
        <div class="relative">
            <div class="w-16 h-16 rounded-full border-4 border-red-500/20 flex items-center justify-center">
                <div class="w-8 h-8 rounded-full bg-red-500 animate-pulse"></div>
            </div>
            <div class="absolute -inset-2 rounded-full border border-red-500/30 animate-ping"></div>
        </div>
        
        <div class="text-center space-y-1">
            <span class="text-sm font-black uppercase tracking-[0.2em] text-red-600">Recording Data...</span>
            <p class="text-[10px] text-muted-foreground italic">Neural capture in progress. Perform interactions.</p>
        </div>

        <button @click="stopGhostRecording()" 
                class="px-10 py-3 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-black transition-all shadow-2xl active:scale-95 border border-white/10">
            End & Finalize
        </button>
    </div>
</div>
