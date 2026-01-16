{{-- Media Registry - Preview Modal --}}
<div x-show="showPreviewModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4 lg:p-20">
    <div @click.away="showPreviewModal = false" class="bg-card w-full max-w-6xl h-full max-h-[85vh] rounded-[40px] shadow-2xl border border-border overflow-hidden flex flex-col lg:flex-row animate-in zoom-in-95 duration-300">
        
        <!-- Asset Section -->
        <div class="flex-1 bg-black flex items-center justify-center relative overflow-hidden group">
            <div class="absolute inset-0 grid-canvas opacity-20 pointer-events-none"></div>
            
            <template x-if="selectedAsset?.type === 'audio'">
                <div class="w-full max-w-md p-10 bg-slate-900/80 backdrop-blur-md rounded-3xl border border-white/10 flex flex-col items-center gap-8 relative z-10 shadow-2xl">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-tr from-primary/20 to-transparent flex items-center justify-center ring-1 ring-primary/30">
                        <i data-lucide="file-audio" class="w-12 h-12 text-primary"></i>
                    </div>
                    <div class="space-y-2 text-center w-full">
                        <h3 class="text-xl font-black text-white uppercase tracking-tight truncate" x-text="selectedAsset?.name"></h3>
                        <p class="text-xs font-mono text-slate-400" x-text="selectedAsset?.metadata?.mime_type || 'AUDIO/WAV'"></p>
                    </div>
                    <audio :src="selectedAsset?.url" controls class="w-full h-12 rounded-lg"></audio>
                </div>
            </template>

            <template x-if="selectedAsset?.type !== 'audio'">
                <img :src="selectedAsset?.url" class="max-w-full max-h-full object-contain relative z-10">
            </template>
        </div>

        <!-- Identity Section -->
        <div class="w-full lg:w-[400px] border-l border-border flex flex-col shrink-0">
            <div class="p-8 border-b border-border bg-muted/30">
                <div class="flex justify-between items-start mb-6">
                    <span class="mono text-[8px] font-black uppercase tracking-[0.4em] text-primary">Identity Context</span>
                    <button @click="showPreviewModal = false" class="w-8 h-8 rounded-full hover:bg-muted transition-colors flex items-center justify-center"><i data-lucide="x" class="w-4 h-4 text-slate-500"></i></button>
                </div>
                <h2 class="text-xl font-black uppercase tracking-tight text-white mb-2" x-text="selectedAsset?.name"></h2>
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest" x-text="'Stored: ' + selectedAsset?.created_at"></p>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                <!-- Source Node -->
                <div class="space-y-3">
                    <label class="text-[9px] font-black uppercase tracking-widest text-primary italic">Provisioning Source</label>
                    <div class="p-4 rounded-2xl bg-muted/20 border border-border flex items-center gap-4">
                        <i :data-lucide="selectedAsset?.source === 'ai_generation' ? 'sparkles' : 'upload'" class="w-5 h-5 text-slate-400"></i>
                        <span class="text-xs font-bold text-foreground uppercase tracking-tight" x-text="selectedAsset?.source"></span>
                    </div>
                </div>

                <!-- AI Protocol Prompt -->
                <template x-if="selectedAsset?.prompt">
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-widest text-primary italic">Original Generation Prompt</label>
                        <div class="p-6 rounded-3xl bg-slate-950/50 border border-white/5 mono text-[10px] text-slate-400 italic leading-relaxed" x-text="selectedAsset?.prompt"></div>
                    </div>
                </template>

                <!-- Technical Specs -->
                <div class="space-y-3">
                    <label class="text-[9px] font-black uppercase tracking-widest text-primary italic">Identity Attributes</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-muted/10 border border-border">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Type</p>
                            <p class="text-[10px] font-bold text-foreground uppercase tracking-widest" x-text="selectedAsset?.type"></p>
                        </div>
                        <div class="p-4 rounded-2xl bg-muted/10 border border-border">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Resolution</p>
                            <p class="text-[10px] font-bold text-foreground uppercase tracking-widest">1024 x 1024</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 border-t border-border bg-muted/30">
                <button class="w-full h-14 bg-primary text-primary-foreground rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                    Synchronize to Content Architect
                </button>
            </div>
        </div>
    </div>
</div>
