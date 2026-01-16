{{-- Day View Modal --}}
<div x-show="showDayModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div @click.away="showDayModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between mb-8 shrink-0">
            <div>
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-1" x-text="formattedSelectedDate()"></h2>
                <p class="text-sm text-muted-foreground italic" x-text="selectedDayPosts.length + ' Scheduled Protocols'"></p>
            </div>
            <button @click="showDayModal = false" class="p-2 hover:bg-muted rounded-full transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="space-y-4 overflow-y-auto flex-1 custom-scrollbar pr-2">
            <template x-for="post in selectedDayPosts" :key="post.id">
                <div class="p-4 rounded-3xl border border-border bg-muted/5 flex items-start gap-4">
                    {{-- Poster Preview --}}
                    <template x-if="post.options && post.options.image_url">
                        <div class="w-24 h-24 rounded-xl overflow-hidden border border-border shrink-0">
                            <img :src="post.options.image_url" class="w-full h-full object-cover">
                        </div>
                    </template>
                    <template x-if="!post.options || !post.options.image_url">
                        <div class="w-24 h-24 rounded-xl bg-muted border border-border flex items-center justify-center shrink-0">
                            <i data-lucide="image-off" class="w-6 h-6 text-muted-foreground/30"></i>
                        </div>
                    </template>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-muted text-muted-foreground" x-text="new Date(post.scheduled_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded" 
                                  :class="post.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-blue-50 text-blue-700'"
                                  x-text="post.status"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-slate-100 text-slate-700" x-text="post.platform"></span>
                        </div>
                        <p class="text-sm text-foreground line-clamp-3 leading-relaxed mb-3" x-text="post.result"></p>
                        
                        <div class="flex items-center gap-2">
                            <button @click="editPost(post.id); showDayModal = false" class="px-3 py-1.5 rounded-lg border border-border text-[10px] font-bold uppercase tracking-wider hover:bg-muted transition-colors">
                                Edit
                            </button>
                            <button @click="deletePost(post.id)" class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 text-[10px] font-bold uppercase tracking-wider hover:bg-red-50 transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="selectedDayPosts.length === 0" class="text-center py-12 text-muted-foreground">
                <i data-lucide="calendar-off" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                <p class="text-xs font-bold uppercase tracking-widest">No protocols scheduled for this cycle.</p>
            </div>
        </div>

        <div class="pt-6 border-t border-border mt-4 shrink-0">
            <button @click="openCreateModal(selectedDay); showDayModal = false" class="w-full h-12 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Protocol to this Day
            </button>
        </div>
    </div>
</div>
