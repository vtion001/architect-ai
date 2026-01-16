{{-- Research Engine - Initiate Form --}}
<div class="lg:col-span-2 space-y-8">
    <div class="rounded-[40px] border border-border bg-card p-10 shadow-xl relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
        
        <div class="flex items-center gap-3 mb-8">
            <i data-lucide="brain" class="w-6 h-6 text-primary"></i>
            <h3 class="text-xl font-black uppercase tracking-tighter">Initiate Research</h3>
        </div>

        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Research Title</label>
                <input x-model="researchTitle" type="text" placeholder="e.g., Q3 Market Intelligence"
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Analytical Query</label>
                <textarea x-model="researchQuery" rows="8" placeholder="Describe the investigative parameters..."
                          class="w-full bg-muted/20 border border-border rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all"></textarea>
            </div>

            <div class="pt-4">
                <button @click="startResearch" :disabled="isResearching" 
                        class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                    <template x-if="!isResearching">
                        <div class="flex items-center gap-2">
                            <i data-lucide="search" class="w-5 h-5"></i>
                            <span>Initiate Sweep</span>
                        </div>
                    </template>
                    <template x-if="isResearching">
                        <div class="flex items-center gap-2">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                            <span>Grounding Data...</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>
