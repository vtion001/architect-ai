{{-- Edit Agent Modal --}}
<div x-show="showEditModal && selectedAgent" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <template x-if="selectedAgent">
        <div @click.away="showEditModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-10 border-b border-border bg-muted/30 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter text-foreground">Reconfigure Agent</h2>
                    <p class="text-[10px] text-muted-foreground font-black uppercase tracking-[0.2em] mt-1" x-text="'NODE: ' + selectedAgent.name"></p>
                </div>
                <button @click="showEditModal = false" class="w-10 h-10 rounded-full hover:bg-muted flex items-center justify-center text-slate-500 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-10 overflow-y-auto max-h-[65vh] custom-scrollbar">
                <div class="space-y-8">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Designation</label>
                            <input x-model="selectedAgent.name" type="text" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Role</label>
                            <input x-model="selectedAgent.role" type="text" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Primary Directive</label>
                        <textarea x-model="selectedAgent.goal" rows="3" class="w-full bg-muted/20 border border-border rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Backstory context</label>
                        <textarea x-model="selectedAgent.backstory" rows="3" class="w-full bg-muted/20 border border-border rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-8 pt-6 border-t border-border/50">
                        <div class="space-y-3">
                            <label class="text-[9px] font-black uppercase text-slate-500 px-1">Primary Signature Color</label>
                            <input x-model="selectedAgent.primary_color" type="color" class="w-full h-14 rounded-2xl cursor-pointer border border-border bg-muted/20 p-1">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[9px] font-black uppercase text-slate-500 px-1">Intelligence Variance (Temp)</label>
                            <div class="flex items-center gap-4 h-14 px-5 bg-muted/20 border border-border rounded-2xl">
                                <input x-model="selectedAgent.temperature" type="range" min="0" max="2" step="0.1" class="flex-1 accent-primary">
                                <span class="mono text-xs font-bold text-primary w-8 text-right" x-text="selectedAgent.temperature"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-8 pt-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" x-model="selectedAgent.is_active" class="w-6 h-6 rounded-lg border-slate-300 text-primary focus:ring-primary transition-all">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-foreground">Protocol Active</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" x-model="selectedAgent.widget_enabled" class="w-6 h-6 rounded-lg border-slate-300 text-primary focus:ring-primary transition-all">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-foreground">Widget Enabled</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="p-8 border-t border-border bg-muted/30 flex justify-end gap-4">
                <button @click="showEditModal = false" class="px-8 py-4 rounded-2xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-white transition-all">Cancel</button>
                <button @click="updateAgent" :disabled="isSaving" class="px-10 py-4 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-3 disabled:opacity-50">
                    <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                    <span x-text="isSaving ? 'SYNCHRONIZING...' : 'Save Reconfiguration'"></span>
                </button>
            </div>
        </div>
    </template>
</div>
