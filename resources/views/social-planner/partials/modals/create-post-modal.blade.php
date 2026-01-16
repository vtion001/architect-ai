{{-- Create/Edit Post Modal --}}
<div x-show="showCreatePostModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div @click.away="showCreatePostModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200">
        <h2 class="text-2xl font-black uppercase tracking-tighter mb-2" x-text="isEditing ? 'Modify Protocol' : 'Schedule Protocol'"></h2>
        <p class="text-sm text-muted-foreground mb-10 italic">Inject a new content node into the campaign timeline.</p>
        
        <form @submit.prevent="schedulePost" class="space-y-8">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Campaign Content</label>
                <textarea x-model="newPostContent" rows="6" class="w-full bg-muted/20 border border-border rounded-3xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Execution Date</label>
                    <input x-model="newPostDate" type="date" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Execution Time</label>
                    <input x-model="newPostTime" type="time" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
            </div>

            <div class="pt-6 flex flex-col gap-3">
                <button type="submit" :disabled="isScheduling" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                    <template x-if="isScheduling">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    </template>
                    <span x-text="isScheduling ? 'PROCESSING...' : (isEditing ? 'UPDATE PROTOCOL' : 'INITIATE SCHEDULE')"></span>
                </button>
                <button type="button" @click="showCreatePostModal = false" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Abort</button>
            </div>
        </form>
    </div>
</div>
