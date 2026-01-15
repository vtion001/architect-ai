<!-- Studio Tab (Ghost Recorder) -->
<div x-show="activeTab === 'studio'" class="space-y-4">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rrweb@latest/dist/rrweb.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/rrweb@latest/dist/rrweb.min.js"></script>

    <div x-show="!isGhostRecording" class="space-y-6 text-center py-4">
        <div class="space-y-2">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20">
                <i data-lucide="ghost" class="w-8 h-8 text-indigo-500"></i>
            </div>
            <h3 class="text-sm font-bold text-foreground">Ghost Demo Recorder</h3>
            <p class="text-[10px] text-muted-foreground max-w-[220px] mx-auto leading-relaxed">
                Record your screen interactions as DOM events. Edit text, hide elements, and polish your demo *after* recording.
            </p>
        </div>

        <button @click="startGhostRecording()" class="w-full py-3 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2">
            <div class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></div>
            Start Recording
        </button>

        <div class="text-left border-t border-border pt-4">
            <h4 class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider mb-3">Recent Demos</h4>
            <div class="space-y-2">
                <template x-for="demo in ghostDemos" :key="demo.id">
                    <div class="flex items-center justify-between p-2.5 bg-card border border-border rounded-lg hover:border-indigo-500/30 transition-colors group cursor-pointer" @click="playDemo(demo.id)">
                        <div class="flex items-center gap-2">
                            <i data-lucide="play-circle" class="w-3.5 h-3.5 text-indigo-500"></i>
                            <span x-text="demo.title" class="text-xs font-medium text-foreground"></span>
                        </div>
                        <span x-text="formatDate(demo.created_at)" class="text-[9px] text-muted-foreground"></span>
                    </div>
                </template>
                <div x-show="ghostDemos.length === 0" class="text-center py-4 text-muted-foreground text-[10px] italic">
                    No demos recorded yet.
                </div>
            </div>
        </div>
    </div>

    <!-- Recording State Overlay (Minimal) -->
    <div x-show="isGhostRecording" class="flex flex-col items-center justify-center py-8 space-y-4">
        <div class="flex items-center gap-2 text-red-500 animate-pulse">
            <i data-lucide="record" class="w-4 h-4 fill-current"></i>
            <span class="text-xs font-black uppercase tracking-widest">Recording DOM...</span>
        </div>
        <p class="text-[10px] text-muted-foreground">Interact with the page normally.</p>
        <button @click="stopGhostRecording()" class="px-6 py-2 bg-foreground text-background rounded-lg text-xs font-bold uppercase hover:opacity-90 transition-opacity">
            Stop & Save
        </button>
    </div>
</div>
