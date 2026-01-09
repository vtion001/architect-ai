{{-- 
    AI Agent Chat Widget Component
    Usage: @include('components.ai-chat-widget', ['agent' => $agent])
    
    This component can be included on any page to add an AI chat widget.
--}}

@props(['agent', 'position' => null])

@php
    $widgetPosition = $position ?? $agent->widget_position ?? 'bottom-right';
    $positionStyles = match($widgetPosition) {
        'bottom-left' => 'bottom: 24px; left: 24px;',
        'top-right' => 'top: 24px; right: 24px;',
        'top-left' => 'top: 24px; left: 24px;',
        default => 'bottom: 24px; right: 24px;',
    };
@endphp

<div id="ai-chat-widget-{{ $agent->id }}"
     x-data="aiChatWidget('{{ $agent->id }}', '{{ $agent->name }}', '{{ $agent->primary_color ?? '#00F2FF' }}', '{{ $agent->welcome_message ?? 'Hello! How can I help you?' }}')" 
     style="position: fixed; {{ $positionStyles }} z-index: 99999;">
    
    {{-- Chat Window --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="mb-4 w-[380px] max-w-[calc(100vw-3rem)] bg-card border border-border rounded-[32px] shadow-2xl overflow-hidden flex flex-col"
         style="height: 520px;">
        
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-border flex items-center justify-between" 
             :style="{ background: `linear-gradient(135deg, ${primaryColor}15, ${primaryColor}05)` }">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-inner"
                     :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                    @if($agent->avatar_url)
                        <img src="{{ $agent->avatar_url }}" alt="{{ $agent->name }}" class="w-10 h-10 rounded-xl object-cover">
                    @else
                        <i data-lucide="bot" class="w-5 h-5"></i>
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-sm text-foreground">{{ $agent->name }}</h3>
                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider">{{ $agent->role }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="clearChat()" class="w-8 h-8 rounded-full hover:bg-muted flex items-center justify-center text-muted-foreground transition-colors" title="Clear chat">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
                <button @click="isOpen = false" class="w-8 h-8 rounded-full hover:bg-muted flex items-center justify-center text-muted-foreground transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
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
                    <div class="bg-muted/50 rounded-2xl rounded-tl-md px-4 py-3 max-w-[80%]">
                        <p class="text-sm text-foreground" x-text="welcomeMessage"></p>
                    </div>
                </div>
            </template>

            {{-- Chat Messages --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex gap-3'">
                    {{-- Assistant Avatar --}}
                    <template x-if="msg.role === 'assistant'">
                        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                             :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                            <i data-lucide="bot" class="w-4 h-4"></i>
                        </div>
                    </template>
                    
                    {{-- Message Bubble --}}
                    <div :class="msg.role === 'user' 
                            ? 'bg-primary text-primary-foreground rounded-2xl rounded-tr-md px-4 py-3 max-w-[80%]' 
                            : 'bg-muted/50 rounded-2xl rounded-tl-md px-4 py-3 max-w-[80%]'">
                        <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>
                    </div>
                </div>
            </template>

            {{-- Typing Indicator --}}
            <div x-show="isTyping" class="flex gap-3">
                <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                     :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                    <i data-lucide="bot" class="w-4 h-4"></i>
                </div>
                <div class="bg-muted/50 rounded-2xl rounded-tl-md px-4 py-3">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 border-t border-border bg-muted/30">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" 
                       x-model="inputMessage" 
                       :disabled="isTyping"
                       placeholder="Type your message..." 
                       class="flex-1 h-11 px-4 rounded-xl border border-border bg-card text-sm focus:ring-2 focus:ring-primary/20 outline-none disabled:opacity-50">
                <button type="submit" 
                        :disabled="!inputMessage.trim() || isTyping"
                        class="w-11 h-11 rounded-xl flex items-center justify-center text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        :style="{ backgroundColor: primaryColor }">
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Toggle Button - Always visible --}}
    <button @click="toggleChat()" 
            class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center text-white transition-all hover:scale-110"
            style="background-color: {{ $agent->primary_color ?? '#00F2FF' }}; box-shadow: 0 8px 32px {{ $agent->primary_color ?? '#00F2FF' }}40;">
        {{-- Use SVG directly for immediate visibility --}}
        <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
        </svg>
        <svg x-show="isOpen" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
        </svg>
    </button>
</div>

{{-- Inline script - define function only once --}}
<script>
if (typeof window.aiChatWidget === 'undefined') {
    window.aiChatWidget = function(agentId, agentName, primaryColor, welcomeMessage) {
        return {
            agentId: agentId,
            agentName: agentName,
            primaryColor: primaryColor,
            welcomeMessage: welcomeMessage,
            isOpen: false,
            isTyping: false,
            inputMessage: '',
            messages: [],
            sessionId: null,

            init() {
                // Get or create session ID
                this.sessionId = localStorage.getItem(`ai_chat_session_${this.agentId}`) || this.generateSessionId();
                localStorage.setItem(`ai_chat_session_${this.agentId}`, this.sessionId);
                
                // Load previous messages
                this.loadConversation();
                
                // Refresh icons after DOM update
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
                try {
                    const response = await fetch('/ai-agents/conversation?' + new URLSearchParams({
                        agent_id: this.agentId,
                        session_id: this.sessionId
                    }), {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
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
                if (!this.inputMessage.trim() || this.isTyping) return;

                const userMessage = this.inputMessage.trim();
                this.inputMessage = '';
                
                // Add user message to UI
                this.messages.push({ role: 'user', content: userMessage });
                this.scrollToBottom();
                
                this.isTyping = true;

                try {
                    const response = await fetch('/ai-agents/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify({
                            agent_id: this.agentId,
                            message: userMessage,
                            session_id: this.sessionId
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.messages.push({ role: 'assistant', content: data.message });
                        this.sessionId = data.session_id;
                        localStorage.setItem(`ai_chat_session_${this.agentId}`, this.sessionId);
                    } else {
                        this.messages.push({ 
                            role: 'assistant', 
                            content: 'Sorry, I encountered an error. Please try again.' 
                        });
                    }
                } catch (e) {
                    console.error('Chat error:', e);
                    this.messages.push({ 
                        role: 'assistant', 
                        content: 'Connection error. Please check your network and try again.' 
                    });
                } finally {
                    this.isTyping = false;
                    this.scrollToBottom();
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            },

            async clearChat() {
                if (!confirm('Clear conversation history?')) return;
                
                try {
                    await fetch('/ai-agents/conversation/clear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
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

