{{-- AI Chat Widget Header --}}
{{-- 
    Expects parent x-data context with:
    primaryColor, selectedBrandId, brands, responseMode, clearChat(), isOpen
    Expects $agent from parent scope
--}}
<div class="px-5 py-4 border-b border-border shrink-0" 
     :style="{ background: `linear-gradient(135deg, ${primaryColor}15, ${primaryColor}05)` }">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden"
                 :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                <template x-if="agentAvatar">
                    <img :src="agentAvatar" :alt="agentName" class="w-10 h-10 object-cover">
                </template>
                <template x-if="!agentAvatar">
                    <i data-lucide="bot" class="w-5 h-5"></i>
                </template>
            </div>
            <div>
                <h3 class="font-bold text-sm text-foreground" x-text="agentName"></h3>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider" x-text="agentRole"></p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <button @click="clearChat()" class="w-8 h-8 rounded-lg hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-muted-foreground transition-colors" title="Clear chat">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
            <button @click="isOpen = false" class="w-8 h-8 rounded-lg hover:bg-muted flex items-center justify-center text-muted-foreground transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <div class="flex items-center gap-2">
        {{-- Brand Selection --}}
        <div x-show="brands.length > 0" class="relative flex-1">
            <select x-model="selectedBrandId" 
                    class="w-full h-8 bg-background/50 border border-border rounded-lg pl-2 pr-8 text-[10px] font-bold uppercase tracking-widest outline-none focus:ring-1 focus:ring-primary/20 appearance-none cursor-pointer">
                <option value="">No Brand Context</option>
                <template x-for="brand in brands" :key="brand.id">
                    <option :value="brand.id" x-text="brand.name"></option>
                </template>
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-muted-foreground pointer-events-none"></i>
        </div>

        {{-- Mode Selection --}}
        <div class="flex bg-background/50 border border-border rounded-lg p-0.5">
            <button @click="responseMode = 'quick'" 
                    :class="responseMode === 'quick' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground'"
                    class="px-2 py-1 rounded-md text-[9px] font-black uppercase tracking-widest transition-all">Quick</button>
            <button @click="responseMode = 'thinking'" 
                    :class="responseMode === 'thinking' ? 'bg-indigo-600 text-white' : 'text-muted-foreground'"
                    class="px-2 py-1 rounded-md text-[9px] font-black uppercase tracking-widest transition-all">Thinking</button>
        </div>
    </div>
</div>
