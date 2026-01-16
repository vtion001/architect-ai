{{-- AI Chat Widget Messages Container --}}
{{-- 
    Expects parent x-data context with:
    messages, welcomeMessage, primaryColor, isTyping, responseMode
--}}
<div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" x-ref="messagesContainer">
    {{-- Welcome Message --}}
    <template x-if="messages.length === 0">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                 :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                <i data-lucide="bot" class="w-4 h-4"></i>
            </div>
            <div class="bg-muted/50 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%]">
                <p class="text-sm text-foreground" x-text="welcomeMessage"></p>
            </div>
        </div>
    </template>

    {{-- Chat Messages --}}
    <template x-for="(msg, index) in messages" :key="index">
        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex gap-3'">
            <template x-if="msg.role === 'assistant'">
                <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                     :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                    <i data-lucide="bot" class="w-4 h-4"></i>
                </div>
            </template>
            
            <div :class="msg.role === 'user' 
                    ? 'text-primary-foreground rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%]' 
                    : 'bg-muted/50 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%]'"
                 :style="msg.role === 'user' ? { backgroundColor: primaryColor } : {}">
                <template x-if="msg.image_url">
                    <div class="mb-2 rounded-lg overflow-hidden border border-white/10">
                        <img :src="msg.image_url" class="max-w-full h-auto object-contain">
                    </div>
                </template>
                <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>
            </div>
        </div>
    </template>

    {{-- Typing Indicator --}}
    <div x-show="isTyping" x-cloak class="flex gap-3">
        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
             :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
            <i data-lucide="bot" class="w-4 h-4"></i>
        </div>
        <div class="bg-muted/50 rounded-2xl rounded-tl-sm px-4 py-3">
            <div class="flex flex-col gap-2">
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
                <template x-if="responseMode === 'thinking'">
                    <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 animate-pulse">Deep Thought Protocol Active...</p>
                </template>
            </div>
        </div>
    </div>
</div>
