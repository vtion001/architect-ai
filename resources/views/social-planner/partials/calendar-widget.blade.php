{{-- Calendar Widget --}}
<div class="lg:col-span-1 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Content Registry Calendar</h3>
    <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
        
        {{-- Month Navigation --}}
        <div class="flex items-center justify-between mb-8">
            <button @click="changeMonth(-1)" class="p-2 hover:bg-muted rounded-full transition-colors">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <div class="text-center">
                <h4 class="text-xl font-black uppercase tracking-tighter" x-text="monthName()"></h4>
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Industrial Timeline</p>
            </div>
            <button @click="changeMonth(1)" class="p-2 hover:bg-muted rounded-full transition-colors">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Day Headers --}}
        <div class="grid grid-cols-7 gap-2 text-center text-[10px] font-black text-slate-500 uppercase mb-4">
            <div>S</div><div>M</div><div>T</div><div>W</div><div>T</div><div>F</div><div>S</div>
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7 gap-2">
            <template x-for="i in startDayOfWeek">
                 <div class="aspect-square opacity-10 border border-transparent"></div>
            </template>
            <template x-for="day in daysInMonth">
                <div @click="viewDay(day)"
                     class="aspect-square rounded-xl border border-border/50 flex flex-col items-center justify-center relative hover:border-primary/50 cursor-pointer transition-all group"
                     :class="{ 'bg-primary text-primary-foreground border-primary': isToday(day) }">
                     <span class="text-[11px] font-black" x-text="day"></span>
                     <div class="absolute bottom-1.5 flex gap-0.5">
                        <template x-for="post in postsOnDay(day).slice(0, 3)">
                            <div class="w-1 h-1 rounded-full" :class="post.status === 'published' ? 'bg-green-500' : 'bg-primary/40 group-hover:bg-primary'"></div>
                        </template>
                        <template x-if="postsOnDay(day).length > 3">
                            <div class="w-1 h-1 rounded-full bg-slate-400"></div>
                        </template>
                     </div>
                </div>
            </template>
        </div>

        {{-- New Schedule Button --}}
        <div class="mt-8 pt-8 border-t border-border/50">
            <button @click="openCreateModal()" class="w-full h-14 bg-muted border border-border rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-white hover:text-black transition-all flex items-center justify-center gap-3">
                <i data-lucide="plus" class="w-4 h-4"></i>
                New Schedule Protocol
            </button>
        </div>
    </div>
</div>
