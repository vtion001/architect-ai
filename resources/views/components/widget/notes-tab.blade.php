<!-- Notes Tab -->
<div x-show="activeTab === 'notes'" class="space-y-4">
    <div class="flex justify-end">
        <button @click="addNote()" class="text-xs font-bold text-primary hover:text-primary/80 flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 rounded-lg transition-colors">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> New Note
        </button>
    </div>
    <div class="grid gap-3">
        <template x-for="note in filteredNotes" :key="note.id">
            <div class="group relative bg-card p-4 border border-border rounded-xl shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                <input type="text" x-model="note.title" @change="updateNote(note)" 
                       class="w-[90%] bg-transparent border-none p-0 text-sm font-bold text-foreground focus:ring-0 mb-2 placeholder:text-muted-foreground/50 transition-colors" placeholder="Untitled Note">
                <textarea x-model="note.description" @change="updateNote(note)" 
                          class="w-full bg-transparent border-none p-0 text-xs text-muted-foreground focus:ring-0 resize-none h-24 custom-scrollbar placeholder:text-muted-foreground/50 leading-relaxed" placeholder="Type your note here..."></textarea>
                
                <button @click="deleteTask(note.id)" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 p-1.5 text-muted-foreground hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
                
                <div class="absolute bottom-3 right-3">
                    <p class="text-[9px] font-mono text-muted-foreground/40 uppercase tracking-widest" x-text="formatDate(note.created_at)"></p>
                </div>
            </div>
        </template>
        
        <div x-show="filteredNotes.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground opacity-50">
            <i data-lucide="sticky-note" class="w-12 h-12 mb-3 stroke-1"></i>
            <p class="text-xs font-medium italic">No matching notes found.</p>
        </div>
    </div>
</div>
