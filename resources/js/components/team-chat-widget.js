// Team Chat Widget Alpine.js Component
// Enables real-time messaging for all users under team management

export function createTeamChatWidgetComponent(currentUser, teamMembers = []) {
    return {
        isOpen: false,
        inputMessage: '',
        messages: [],
        teamMembers: teamMembers,
        selectedRecipient: null, // null = team channel, otherwise direct message
        isTyping: false,
        typingUsers: [],
        unreadCount: 0,
        currentUser: currentUser,

        init() {
            this.loadMessages();
            this.setupRealtime();
        },

        open() {
            this.isOpen = true;
            this.unreadCount = 0;
            this.$nextTick(() => this.scrollToBottom());
        },
        close() {
            this.isOpen = false;
        },
        sendMessage() {
            if (!this.inputMessage.trim()) return;
            const msg = {
                sender: this.currentUser,
                recipient: this.selectedRecipient,
                text: this.inputMessage,
                timestamp: new Date().toISOString(),
            };
            this.messages.push(msg);
            this.inputMessage = '';
            this.saveMessages();
            this.scrollToBottom();
            // TODO: Integrate with backend or websocket for real-time
        },
        selectRecipient(user) {
            this.selectedRecipient = user;
            this.loadMessages();
        },
        loadMessages() {
            // TODO: Load from backend or localStorage
            // For now, just clear unread
            this.unreadCount = 0;
        },
        saveMessages() {
            // TODO: Save to backend or localStorage
        },
        setupRealtime() {
            // TODO: Integrate with websocket for real-time updates
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs && this.$refs.messagesEnd;
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            });
        },
    };
}
