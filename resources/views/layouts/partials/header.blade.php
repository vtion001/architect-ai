{{-- Main Header Bar --}}
<header class="bg-card border-b border-border px-8 py-4 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-semibold text-foreground">Welcome Back to ArchitGrid</h2>
    </div>

    <div class="flex items-center gap-4">
        {{-- Search Trigger --}}
        <div class="relative group cursor-pointer" @click="showCommandPalette = true">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors"></i>
            <div class="pl-9 w-80 bg-background flex h-10 w-full items-center rounded-md border border-input px-3 text-sm text-muted-foreground hover:border-primary/30 transition-all">
                Architect your next move...
                <span class="ml-auto mono text-[9px] font-black border border-border px-1.5 py-0.5 rounded uppercase opacity-50">Ctrl K</span>
            </div>
        </div>
        
        {{-- Messages Button --}}
        <div x-data="window.createTeamChatWidgetComponent(@js(auth()->user()), @js($teamMembers ?? []))">
            <button @click="open" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10">
                <i data-lucide="message-square" class="w-5 h-5"></i>
                <template x-if="unreadCount > 0">
                    <span class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full px-1" x-text="unreadCount"></span>
                </template>
            </button>
            <template x-if="isOpen">
                <div class="fixed z-50 bottom-4 right-4 w-96 bg-background border border-border rounded-lg shadow-lg flex flex-col" @click.away="close">
                    <div class="flex items-center justify-between px-4 py-2 border-b border-border">
                        <span class="font-semibold">Team Chat</span>
                        <button @click="close" class="text-muted-foreground hover:text-foreground"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-4 py-2 space-y-2" style="max-height: 300px;">
                        <template x-for="msg in messages" :key="msg.timestamp">
                            <div :class="{'text-right': msg.sender.id === currentUser.id}">
                                <div class="inline-block px-3 py-2 rounded-lg" :class="msg.sender.id === currentUser.id ? 'bg-primary text-white' : 'bg-muted text-foreground'">
                                    <span x-text="msg.text"></span>
                                </div>
                                <div class="text-xs text-muted-foreground mt-1" x-text="msg.sender.name"></div>
                            </div>
                        </template>
                        <div x-ref="messagesEnd"></div>
                    </div>
                    <div class="flex items-center border-t border-border px-4 py-2">
                        <input x-model="inputMessage" @keydown.enter="sendMessage" type="text" class="flex-1 bg-transparent outline-none" placeholder="Message your team..." />
                        <button @click="sendMessage" class="ml-2 text-primary hover:text-primary-foreground"><i data-lucide="send" class="w-5 h-5"></i></button>
                    </div>
                </div>
            </template>
        </div>
        
        {{-- Notifications Dropdown --}}
        @include('layouts.partials.header.notifications')
        
        {{-- User Avatar --}}
        <div class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-full">
            <div class="flex h-full w-full items-center justify-center rounded-full bg-muted">AA</div>
        </div>
    </div>
</header>
