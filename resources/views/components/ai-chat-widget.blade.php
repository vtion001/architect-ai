{{-- 
    AI Agent Chat Widget Component
    Usage: @include('components.ai-chat-widget', ['agent' => $agent])
    
    This component can be included on any page to add an AI chat widget.
    The chat appears as a floating popup in the corner, NOT fullscreen.
--}}

@props(['agent', 'position' => null])

@php
    $widgetPosition = $position ?? $agent->widget_position ?? 'bottom-right';
    $primaryColor = $agent->primary_color ?? '#00F2FF';
    
    // Position for the floating trigger button
    $buttonPosition = match($widgetPosition) {
        'bottom-left' => 'bottom: 24px; left: 24px;',
        'top-right' => 'top: 24px; right: 24px;',
        'top-left' => 'top: 24px; left: 24px;',
        default => 'bottom: 24px; right: 24px;',
    };
    
    // Position for the chat popup (above/below the button)
    $chatPosition = match($widgetPosition) {
        'bottom-left' => 'bottom: 90px; left: 24px;',
        'top-right' => 'top: 90px; right: 24px;',
        'top-left' => 'top: 90px; left: 24px;',
        default => 'bottom: 90px; right: 24px;',
    };
@endphp

{{-- Main Widget Container --}}
<div id="ai-chat-widget-{{ $agent->id }}"
     x-data="aiChatWidget('{{ $agent->id }}', '{{ $agent->name }}', '{{ $primaryColor }}', '{{ $agent->welcome_message ?? 'Hello! How can I help you?' }}')"
     @open-ai-chat.window="if($event.detail && $event.detail.id === '{{ $agent->id }}') isOpen = true">
    
    {{-- Chat Popup Window - Fixed size, NOT fullscreen --}}
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         style="position: fixed; {{ $chatPosition }} z-index: 99998; width: 380px; max-width: calc(100vw - 48px); height: 520px; max-height: calc(100vh - 140px);"
         class="bg-card border border-border rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-border flex items-center justify-between shrink-0" 
             :style="{ background: `linear-gradient(135deg, ${primaryColor}15, ${primaryColor}05)` }">
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
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 border-t border-border bg-muted/20 shrink-0">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" 
                       x-model="inputMessage" 
                       :disabled="isTyping"
                       placeholder="Type your message..." 
                       class="flex-1 h-10 px-4 rounded-xl border border-border bg-card text-sm focus:ring-2 focus:ring-primary/20 outline-none disabled:opacity-50">
                <button type="submit" 
                        :disabled="!inputMessage.trim() || isTyping"
                        class="w-10 h-10 rounded-xl flex items-center justify-center text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 active:scale-95"
                        :style="{ backgroundColor: primaryColor }">
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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
                this.messages.push({ role: 'user', content: userMessage });
                this.scrollToBottom();
                this.isTyping = true;

                try {
                    const response = await fetch('/ai-agents/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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
