{{-- Bulk Schedule Modal Partial --}}
<div x-show="$store.cc.showBulkScheduleModal" 
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
     x-transition>
    
    <div @click.away="$store.cc.showBulkScheduleModal = false" 
         class="bg-card w-full max-w-md rounded-2xl shadow-2xl border border-border p-6 space-y-6 relative animate-in zoom-in-95 duration-200">
        
        <div class="text-center">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="calendar-clock" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="text-lg font-bold text-foreground">Bulk Schedule Calendar</h3>
            <p class="text-xs text-muted-foreground mt-1">Distribute your strategy across the week.</p>
        </div>

        {{-- Start Date Input --}}
        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Start Date</label>
            <input type="date" x-model="$store.cc.bulkStartDate" 
                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-green-500/20 outline-none transition-all">
            <p class="text-[10px] text-muted-foreground italic">Posts will be distributed over 7 days starting from this date.</p>
        </div>

        {{-- Platform Selection --}}
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Target Platforms</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-all" 
                       :class="$store.cc.bulkPlatforms.includes('facebook') ? 'border-blue-600 bg-blue-50 ring-1 ring-blue-600/20' : ''">
                    <input type="checkbox" value="facebook" x-model="$store.cc.bulkPlatforms" class="hidden">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm">Fb</div>
                    <span class="text-xs font-bold uppercase tracking-wider">Facebook</span>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-all" 
                       :class="$store.cc.bulkPlatforms.includes('linkedin') ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500/20' : ''">
                    <input type="checkbox" value="linkedin" x-model="$store.cc.bulkPlatforms" class="hidden">
                    <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-white shadow-sm">Li</div>
                    <span class="text-xs font-bold uppercase tracking-wider">LinkedIn</span>
                </label>
                
                <label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-all" 
                       :class="$store.cc.bulkPlatforms.includes('instagram') ? 'border-pink-500 bg-pink-50 ring-1 ring-pink-500/20' : ''">
                    <input type="checkbox" value="instagram" x-model="$store.cc.bulkPlatforms" class="hidden">
                    <div class="w-8 h-8 rounded-lg bg-pink-600 flex items-center justify-center text-white shadow-sm">Ig</div>
                    <span class="text-xs font-bold uppercase tracking-wider">Instagram</span>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-all" 
                       :class="$store.cc.bulkPlatforms.includes('twitter') ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-500/20' : ''">
                    <input type="checkbox" value="twitter" x-model="$store.cc.bulkPlatforms" class="hidden">
                    <div class="w-8 h-8 rounded-lg bg-sky-400 flex items-center justify-center text-white shadow-sm">Tw</div>
                    <span class="text-xs font-bold uppercase tracking-wider">Twitter</span>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pt-2">
            <button @click="$store.cc.showBulkScheduleModal = false" 
                    class="flex-1 h-12 rounded-xl border border-border font-bold uppercase text-[10px] tracking-widest hover:bg-muted transition-all">
                Cancel
            </button>
            <button @click="$store.cc.confirmBulkSchedule()" 
                    :disabled="$store.cc.isBulkScheduling || $store.cc.bulkPlatforms.length === 0"
                    class="flex-1 h-12 rounded-xl bg-green-600 text-white font-bold uppercase text-[10px] tracking-widest shadow-lg shadow-green-500/20 hover:bg-green-700 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="!$store.cc.isBulkScheduling">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </template>
                <template x-if="$store.cc.isBulkScheduling">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                </template>
                <span x-text="$store.cc.isBulkScheduling ? 'Scheduling...' : 'Confirm Schedule'"></span>
            </button>
        </div>
    </div>
</div>
