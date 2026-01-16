{{-- AI Chat Widget Input Area --}}
{{-- 
    Expects parent x-data context with:
    attachmentPreview, clearAttachment(), sendMessage(), handleAttachment(),
    inputMessage, isTyping, attachment, responseMode, primaryColor
--}}
<div class="p-4 border-t border-border bg-muted/20 shrink-0 space-y-3">
    {{-- Attachment Preview --}}
    <template x-if="attachmentPreview">
        <div class="relative w-20 h-20 rounded-xl overflow-hidden border border-primary animate-in zoom-in-95">
            <img :src="attachmentPreview" class="w-full h-full object-cover">
            <button @click="clearAttachment()" class="absolute top-1 right-1 w-5 h-5 bg-black/60 rounded-full flex items-center justify-center text-white">
                <i data-lucide="x" class="w-3 h-3"></i>
            </button>
        </div>
    </template>

    <form @submit.prevent="sendMessage" class="flex gap-2">
        <input type="file" x-ref="attachmentInput" @change="handleAttachment" class="hidden" accept="image/*">
        <button type="button" @click="$refs.attachmentInput.click()" class="w-10 h-10 rounded-xl border border-border bg-card flex items-center justify-center text-muted-foreground hover:text-primary hover:border-primary/30 transition-all">
            <i data-lucide="paperclip" class="w-4 h-4"></i>
        </button>
        
        <input type="text" 
               x-model="inputMessage" 
               :disabled="isTyping"
               placeholder="Ask or upload an image..." 
               class="flex-1 h-10 px-4 rounded-xl border border-border bg-card text-sm focus:ring-2 focus:ring-primary/20 outline-none disabled:opacity-50">
        
        <button type="submit" 
                :disabled="(!inputMessage.trim() && !attachment) || isTyping"
                class="w-10 h-10 rounded-xl flex items-center justify-center text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 active:scale-95"
                :style="{ backgroundColor: responseMode === 'thinking' ? '#4f46e5' : primaryColor }">
            <i data-lucide="send" class="w-4 h-4"></i>
        </button>
    </form>
</div>
