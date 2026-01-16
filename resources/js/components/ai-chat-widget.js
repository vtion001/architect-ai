/**
 * AI Chat Widget Alpine.js Component
 */

/**
 * Creates the AI Chat Widget Alpine.js component
 */
export function createAiChatWidgetComponent(initialAgentId, initialName, initialColor, initialWelcome, brands = [], initialRole = 'AI Assistant', initialAvatar = null) {
    return {
        agentId: initialAgentId,
        agentName: initialName,
        primaryColor: initialColor,
        welcomeMessage: initialWelcome,
        agentRole: initialRole,
        agentAvatar: initialAvatar,
        brands: brands,
        
        selectedBrandId: '',
        responseMode: 'quick',
        attachment: null,
        attachmentPreview: null,
        isOpen: false,
        isTyping: false,
        inputMessage: '',
        messages: [],
        sessionId: null,

        init() {
            this.setupSession();
            this.loadConversation();
            
            // Register store logic if not already present (idempotent check)
            if (typeof Alpine !== 'undefined' && !Alpine.store('aiChat')) {
                Alpine.store('aiChat', {
                    isOpen: false,
                    activeAgentId: null,
                    openWithAgent(agent) {
                        this.activeAgentId = agent.id;
                        this.isOpen = true;
                        window.dispatchEvent(new CustomEvent('ai-chat-switch-agent', { 
                            detail: { 
                                id: agent.id,
                                name: agent.name,
                                primaryColor: agent.primary_color,
                                welcomeMessage: agent.welcome_message,
                                role: agent.role,
                                avatar_url: agent.avatar_url
                            } 
                        }));
                    }
                });
            }

            // Sync initial state to store if not already set
            if (this.agentId && typeof Alpine !== 'undefined' && Alpine.store('aiChat') && !Alpine.store('aiChat').activeAgentId) {
                Alpine.store('aiChat').activeAgentId = this.agentId;
            }

            // Sync component visibility with global store
            if (typeof Alpine !== 'undefined' && Alpine.store('aiChat')) {
                this.$watch('$store.aiChat.isOpen', value => {
                    this.isOpen = value;
                    if (value) {
                        this.$nextTick(() => this.scrollToBottom());
                    }
                });
            }

            // Handle global agent switching events
            window.addEventListener('ai-chat-switch-agent', (e) => {
                const agent = e.detail;
                if (this.agentId !== agent.id) {
                    this.agentId = agent.id;
                    this.agentName = agent.name;
                    this.primaryColor = agent.primaryColor || '#00F2FF';
                    this.welcomeMessage = agent.welcomeMessage || 'Hello!';
                    this.agentRole = agent.role || 'AI Assistant';
                    this.agentAvatar = agent.avatar_url || null;
                    this.messages = [];
                    this.setupSession();
                    this.loadConversation();
                }
                this.isOpen = true;
                if(typeof Alpine !== 'undefined' && Alpine.store('aiChat')) {
                    Alpine.store('aiChat').isOpen = true;
                }
            });

            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        setupSession() {
            this.sessionId = localStorage.getItem(`ai_chat_session_${this.agentId}`) || this.generateSessionId();
            localStorage.setItem(`ai_chat_session_${this.agentId}`, this.sessionId);
        },

        generateSessionId() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
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
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            };
            reader.readAsDataURL(file);
        },

        clearAttachment() {
            this.attachment = null;
            this.attachmentPreview = null;
            if (this.$refs.attachmentInput) this.$refs.attachmentInput.value = '';
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if(typeof Alpine !== 'undefined' && Alpine.store('aiChat')) {
                Alpine.store('aiChat').isOpen = this.isOpen;
            }
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                    if (window.lucide) window.lucide.createIcons();
                });
            }
        },

        async loadConversation() {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token) return;

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
            }, 2000);

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
}

// Expose globally for fallback usage (fixes ReferenceError if Alpine inits early)
if (typeof window !== 'undefined') {
    window.aiChatWidget = createAiChatWidgetComponent;
    
    // Also register with Alpine if it loads later
    document.addEventListener('alpine:init', () => {
        // Register Global Store
        if (!Alpine.store('aiChat')) {
            Alpine.store('aiChat', {
                isOpen: false,
                activeAgentId: null,
                openWithAgent(agent) {
                    this.activeAgentId = agent.id;
                    this.isOpen = true;
                    window.dispatchEvent(new CustomEvent('ai-chat-switch-agent', { 
                        detail: { 
                            id: agent.id,
                            name: agent.name,
                            primaryColor: agent.primary_color,
                            welcomeMessage: agent.welcome_message,
                            role: agent.role,
                            avatar_url: agent.avatar_url
                        } 
                    }));
                }
            });
        }

        // Register Component
        Alpine.data('aiChatWidget', createAiChatWidgetComponent);
    });
}
