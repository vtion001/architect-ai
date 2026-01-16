{{-- Notifications Dropdown --}}
<div class="relative" x-data="{ 
    openAlerts: false, 
    unreadCount: {{ auth()->user()->unreadNotifications->count() }},
    markAllAsRead() {
        fetch('{{ route('notifications.read-all') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(() => { this.unreadCount = 0; window.location.reload(); });
    }
}">
    <button @click="openAlerts = !openAlerts" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10 relative">
        <i data-lucide="bell" class="w-5 h-5"></i>
        <template x-if="unreadCount > 0">
            <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full ring-2 ring-background animate-pulse"></span>
        </template>
    </button>

    <div x-show="openAlerts" @click.away="openAlerts = false" x-cloak
         class="absolute right-0 mt-4 w-96 bg-card border border-border rounded-3xl shadow-2xl z-[150] overflow-hidden animate-in slide-in-from-top-2 duration-200">
        
        <div class="p-6 border-b border-border bg-muted/30 flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-tighter">Intelligence Feed</h3>
            <template x-if="unreadCount > 0">
                <button @click="markAllAsRead" class="text-[9px] font-black uppercase text-primary hover:underline">Clear Registry</button>
            </template>
        </div>

        <div class="max-h-[400px] overflow-y-auto p-4 space-y-3 custom-scrollbar">
            @forelse(auth()->user()->notifications as $notification)
                <div class="p-4 rounded-2xl bg-muted/20 border border-border hover:border-primary/30 transition-all group relative">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0 border border-primary/20">
                            <i data-lucide="{{ $notification->data['icon'] ?? 'bell' }}" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black uppercase tracking-tight text-foreground">{{ $notification->data['title'] }}</p>
                            <p class="text-[11px] text-muted-foreground italic leading-relaxed mt-1">{{ $notification->data['message'] }}</p>
                            <p class="text-[8px] font-mono text-slate-500 mt-2 uppercase tracking-widest">{{ \Carbon\Carbon::parse($notification->data['timestamp'])->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if(isset($notification->data['action_url']))
                        <a href="{{ $notification->data['action_url'] }}" class="absolute inset-0 z-10"></a>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center opacity-30 italic">
                    <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-3"></i>
                    <p class="text-xs font-bold uppercase tracking-widest">Feed is idle</p>
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-border bg-muted/30 text-center">
            <p class="mono text-[8px] font-black uppercase tracking-[0.4em] text-slate-500">Grid Alert Protocol v1.0</p>
        </div>
    </div>
</div>
