{{-- Users Management - Invitation Card --}}
<div class="p-4 rounded-2xl bg-muted/20 border border-border hover:bg-muted/30 transition-all relative overflow-hidden group">
    <div class="flex flex-col">
        <span class="text-[10px] font-black text-foreground truncate">{{ $invite->email }}</span>
        <span class="text-[8px] font-bold text-primary uppercase tracking-widest mt-1">{{ $invite->role->name }}</span>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <span class="text-[8px] font-mono text-slate-500">Exp: {{ $invite->expires_at->format('M d') }}</span>
        <button class="text-red-500 hover:text-red-600 transition-colors">
            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
        </button>
    </div>
    <!-- Copy Link Button -->
    <button @click="navigator.clipboard.writeText('{{ url('/auth/join/'.$invite->token) }}'); alert('Invitation link copied to grid buffer.');"
            class="absolute top-2 right-2 p-1.5 rounded-lg bg-white opacity-0 group-hover:opacity-100 shadow-sm transition-all hover:scale-105" title="Copy Invitation Link">
        <i data-lucide="copy" class="w-3 h-3 text-primary"></i>
    </button>
</div>
