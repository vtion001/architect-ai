<!-- History Tab -->
<div x-show="activeTab === 'history'" class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Recently Deleted</h3>
        <button @click="fetchHistory()" class="text-[10px] font-bold text-primary hover:underline">Refresh</button>
    </div>
    
    <div class="space-y-2">
        <template x-for="item in history" :key="item.id">
            <div class="flex items-center justify-between p-3 bg-muted/20 border border-border rounded-xl opacity-75 hover:opacity-100 transition-opacity">
                <div class="min-w-0 flex-1 mr-3">
                    <p x-text="item.title" class="text-xs font-medium truncate text-foreground"></p>
                    <p class="text-[9px] text-muted-foreground uppercase tracking-wide" x-text="item.type + ' • ' + formatDate(item.deleted_at)"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="restoreItem(item.id)" class="p-1.5 bg-primary/10 text-primary rounded-lg hover:bg-primary hover:text-white transition-colors" title="Restore">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </button>
                    <button @click="forceDeleteItem(item.id)" class="p-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors" title="Delete Forever">
                        <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        </template>
        
        <div x-show="history.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground opacity-50">
            <i data-lucide="trash-2" class="w-8 h-8 mb-2 stroke-1"></i>
            <p class="text-xs font-medium italic">Trash is empty.</p>
        </div>
    </div>
</div>
