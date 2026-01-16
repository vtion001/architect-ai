{{-- AI Chat Widget Toggle Button --}}
{{-- 
    Expects parent x-data context with: isOpen, toggleChat(), agentAvatar, agentName, primaryColor
    Expects $buttonPosition from parent scope
--}}
<button @click="toggleChat()" 
        style="position: fixed; {{ $buttonPosition }} z-index: 99999;"
        :style="{ backgroundColor: primaryColor, boxShadow: `0 8px 24px ${primaryColor}40` }"
        class="w-14 h-14 rounded-full shadow-xl flex items-center justify-center text-white transition-all hover:scale-110 active:scale-95 group overflow-hidden border-2 border-white/20">
    
    {{-- Avatar Head (Shown when closed) --}}
    <div x-show="!isOpen" class="relative w-full h-full flex items-center justify-center">
        <template x-if="agentAvatar">
            <img :src="agentAvatar" :alt="agentName" class="w-full h-full object-cover">
        </template>
        <template x-if="!agentAvatar">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7V5.73C7.4 5.39 7 4.74 7 4a2 2 0 0 1 2-2h3z"/>
                <path d="M8 12a2 2 0 1 0 4 0 2 2 0 0 0-4 0z"/>
                <path d="M12 12a2 2 0 1 0 4 0 2 2 0 0 0-4 0z"/>
            </svg>
        </template>
        
        {{-- Pulse Status Indicator --}}
        <span class="absolute top-1 right-1 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border-2 border-white"></span>
        </span>
    </div>

    {{-- Minimize Icon (Shown when open) --}}
    <svg x-show="isOpen" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
        <path d="m18 8-6 6-6-6"/>
    </svg>
</button>
