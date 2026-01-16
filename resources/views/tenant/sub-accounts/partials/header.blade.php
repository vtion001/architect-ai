{{-- Sub-Accounts - Page Header --}}
<div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
    <div>
        <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Sub-Account Explorer</h1>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest" x-text="'Grid Capacity: ' + capacity.current + '/' + capacity.max + ' Nodes Active'"></span>
            </div>
            <span class="text-[10px] font-mono text-slate-400 uppercase tracking-tighter italic" x-text="'Active Protocol: ' + capacity.label"></span>
        </div>
    </div>
    <div class="flex gap-4">
        <div class="hidden lg:flex flex-col items-end justify-center px-6 border-r border-border">
            <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-1">Network Quota</p>
            <div class="w-32 h-1.5 bg-muted rounded-full overflow-hidden">
                <div class="bg-primary h-full transition-all duration-1000" :style="'width: ' + (capacity.current / capacity.max * 100) + '%'"></div>
            </div>
        </div>
        <button @click="showAddModal = true" :disabled="capacity.current >= capacity.max" 
                class="bg-primary text-primary-foreground px-8 h-14 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 flex items-center gap-3 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:grayscale">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Provision Node
        </button>
    </div>
</div>
