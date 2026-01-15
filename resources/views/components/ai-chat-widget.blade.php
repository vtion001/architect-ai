{{-- 
    AI Agent Chat Widget Component
    Usage: @include('components.ai-chat-widget', ['agent' => $agent])
    
    This component can be included on any page to add an AI chat widget.
    The chat appears as a floating popup in the corner, NOT fullscreen.
--}}

@props(['agent', 'position' => null, 'brands' => null])

@php
    $widgetPosition = $position ?? $agent->widget_position ?? 'bottom-right';
    $primaryColor = $agent->primary_color ?? '#00F2FF';
    
    // Fallback: If brands are not passed (e.g. from layout), fetch them here.
    if ($brands === null && auth()->check() && auth()->user()->tenant) {
        $brands = \App\Models\Brand::where('tenant_id', auth()->user()->tenant_id)
            ->select('id', 'name')
            ->get();
    } else {
        $brands = $brands ?? [];
    }
    
    // Position for the floating trigger button
    $buttonPosition = match($widgetPosition) {
        'bottom-left' => 'bottom: 24px; left: 24px;',
        'top-right' => 'top: 24px; right: 24px;',
        'top-left' => 'top: 24px; left: 24px;',
        default => 'bottom: 24px; right: 24px;',
    };
    
    // Position for the chat popup (shifted to the side of the button to avoid overlap)
    $chatPosition = match($widgetPosition) {
        'bottom-left' => 'bottom: 24px; left: 94px;',
        'top-right' => 'top: 24px; right: 94px;',
        'top-left' => 'top: 24px; left: 94px;',
        default => 'bottom: 24px; right: 94px;',
    };
@endphp

{{-- Main Widget Container --}}
<div id="ai-chat-widget-{{ $agent->id }}"
     x-data="aiChatWidget('{{ $agent->id }}', '{{ $agent->name }}', '{{ $primaryColor }}', '{{ $agent->welcome_message ?? 'Hello! How can I help you?' }}', {{ json_encode($brands) }})"
     @open-ai-chat.window="if($event.detail && $event.detail.id === '{{ $agent->id }}') isOpen = true">
    
    {{-- Chat Popup Window --}}
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-x-4"
         x-transition:enter-end="opacity-100 scale-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-x-0"
         x-transition:leave-end="opacity-0 scale-95 translate-x-4"
         style="position: fixed; {{ $chatPosition }} z-index: 99998; width: 380px; max-width: calc(100vw - 48px); height: 580px; max-height: calc(100vh - 48px);"
         class="bg-card border border-border rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-border shrink-0" 
             :style="{ background: `linear-gradient(135deg, ${primaryColor}15, ${primaryColor}05)` }">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                         :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                        @if($agent->avatar_url)
                            <img src="{{ $agent->avatar_url }}" alt="{{ $agent->name }}" class="w-10 h-10 rounded-xl object-cover">
                        @else
                            <i data-lucide="bot" class="w-5 h-5"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-foreground">{{ $agent->name }}</h3>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <p class="text-[10px] text-muted-foreground uppercase tracking-wider">{{ $agent->role ?? 'AI Assistant' }}</p>
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

        {{-- Messages Container --}}
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
                            ? 'bg-primary text-primary-foreground rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%]' 
                            : 'bg-muted/50 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%]'">
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

        {{-- Input Area --}}
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
    </div>

    {{-- Floating Toggle Button --}}
    <button @click="toggleChat()" 
            style="position: fixed; {{ $buttonPosition }} z-index: 99999; background-color: {{ $primaryColor }}; box-shadow: 0 8px 24px {{ $primaryColor }}40;"
            class="w-14 h-14 rounded-full shadow-xl flex items-center justify-center text-white transition-all hover:scale-110 active:scale-95">
        <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
        </svg>
        <svg x-show="isOpen" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
        </svg>
    </button>
</div>

{{-- Inline script --}}
<script>
if (typeof window.aiChatWidget === 'undefined') {
    window.aiChatWidget = function(agentId, agentName, primaryColor, welcomeMessage, brands = []) {
        return {
            agentId: agentId,
            agentName: agentName,
            primaryColor: primaryColor,
            welcomeMessage: welcomeMessage,
            brands: brands,
            selectedBrandId: '',
            responseMode: 'quick', // 'quick' or 'thinking'
            attachment: null,
            attachmentPreview: null,
            isOpen: false,
            isTyping: false,
            inputMessage: '',
            messages: [],
            sessionId: null,

            init() {
                this.sessionId = localStorage.getItem(`ai_chat_session_${this.agentId}`) || this.generateSessionId();
                localStorage.setItem(`ai_chat_session_${this.agentId}`, this.sessionId);
                this.loadConversation();
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            },

            generateSessionId() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0;
                    const v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            },

            handleAttachment(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.attachment = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.attachmentPreview = e.target.result;
                    this.$nextTick(() => lucide.createIcons());
                };
                reader.readAsDataURL(file);
            },

            clearAttachment() {
                this.attachment = null;
                this.attachmentPreview = null;
                this.$refs.attachmentInput.value = '';
            },

            toggleChat() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.$nextTick(() => {
                        this.scrollToBottom();
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            },

            async loadConversation() {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!token) {
                    console.warn('CSRF token missing for AI Chat load');
                    return;
                }

                try {
                    const response = await fetch('/ai-agents/conversation?' + new URLSearchParams({
                        agent_id: this.agentId,
                        session_id: this.sessionId
                    }), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.messages) {
                            this.messages = data.messages;
                        }
                    }
                } catch (e) {
                    console.error('Failed to load conversation:', e);
                }
            },

            async sendMessage() {
                if ((!this.inputMessage.trim() && !this.attachment) || this.isTyping) return;

                const userMessage = this.inputMessage.trim();
                const currentAttachment = this.attachment;
                const currentPreview = this.attachmentPreview;
                
                this.inputMessage = '';
                this.clearAttachment();

                // Add to local message list immediately
                this.messages.push({ 
                    role: 'user', 
                    content: userMessage,
                    image_url: currentPreview 
                });
                
                this.scrollToBottom();
                this.isTyping = true;

                const formData = new FormData();
                formData.append('agent_id', this.agentId);
                formData.append('message', userMessage);
                formData.append('session_id', this.sessionId);
                formData.append('brand_id', this.selectedBrandId);
                formData.append('mode', this.responseMode);
                if (currentAttachment) {
                    formData.append('image', currentAttachment);
                }

                try {
                    const response = await fetch('/ai-agents/chat', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.startPolling();
                    } else {
                        this.messages.push({ 
                            role: 'assistant', 
                            content: 'Agent error: ' + (data.message || 'Unknown failure') 
                        });
                        this.isTyping = false;
                    }
                } catch (e) {
                    console.error('Chat error:', e);
                    this.messages.push({ 
                        role: 'assistant', 
                        content: 'Connection lost. Please try again in a moment.' 
                    });
                    this.isTyping = false;
                } finally {
                    this.scrollToBottom();
                }
            },

            startPolling() {
                const pollInterval = setInterval(async () => {
                    await this.loadConversation();
                    
                    const lastMsg = this.messages[this.messages.length - 1];
                    if (lastMsg && lastMsg.role === 'assistant') {
                        clearInterval(pollInterval);
                        this.isTyping = false;
                        this.scrollToBottom();
                    }
                }, 2000); // Polling every 2s for responsiveness
                
                // Stop after 60s max
                setTimeout(() => clearInterval(pollInterval), 60000);
            },

            async clearChat() {
                if (!confirm('Clear conversation history?')) return;
                
                try {
                    await fetch('/ai-agents/conversation/clear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            agent_id: this.agentId,
                            session_id: this.sessionId
                        })
                    });
                } catch (e) {
                    console.error('Failed to clear conversation:', e);
                }
                
                this.messages = [];
                this.sessionId = this.generateSessionId();
                localStorage.setItem(`ai_chat_session_${this.agentId}`, this.sessionId);
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            }
        };
    };
}
</script>
