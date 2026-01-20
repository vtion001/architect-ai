<!-- Studio Tab (Ghost Recorder) - Coming Soon -->
<div x-show="activeTab === 'studio'" class="space-y-4">
    <div class="space-y-6 text-center py-4">
        <div class="space-y-2">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 shadow-inner relative">
                <i data-lucide="ghost" class="w-10 h-10 text-indigo-600"></i>
                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-amber-500 flex items-center justify-center">
                    <i data-lucide="clock" class="w-3 h-3 text-white"></i>
                </div>
            </div>
            <h3 class="text-lg font-black text-foreground uppercase tracking-tight">Ghost Studio</h3>
            <p class="text-xs text-muted-foreground max-w-[240px] mx-auto leading-relaxed italic">
                Capture seamless DOM interactions. Record, edit, and replay your app demos with industrial precision.
            </p>
        </div>

        <div class="py-4 px-6 bg-amber-500/10 border border-amber-500/20 rounded-2xl">
            <div class="flex items-center justify-center gap-2 mb-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                <span class="text-xs font-black text-amber-600 uppercase tracking-widest">Coming Soon</span>
            </div>
            <p class="text-[10px] text-muted-foreground">
                Advanced screen recording with AI-powered editing is under development.
            </p>
        </div>

        <button disabled 
                class="w-full py-4 bg-indigo-600/50 text-white/70 rounded-2xl text-xs font-black uppercase tracking-[0.2em] cursor-not-allowed flex items-center justify-center gap-3 opacity-60">
            <div class="w-2 h-2 rounded-full bg-red-400"></div>
            Start Neural Recording
        </button>

        <div class="text-left border-t border-border pt-6 mt-2">
            <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                <i data-lucide="database" class="w-3 h-3"></i>
                Saved Demo Archives
            </h4>
            <div class="space-y-2.5">
                <div class="text-center py-8 text-muted-foreground/50 text-[10px] italic border-2 border-dashed border-border rounded-xl bg-muted/5">
                    Feature coming in a future update.
                </div>
            </div>
        </div>
    </div>
</div>
