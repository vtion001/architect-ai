{{-- Sidebar Footer --}}
<div class="p-4 border-t border-sidebar-border">
    @if(session()->has('impersonated_by'))
        <div class="mb-4 p-4 rounded-xl bg-red-600 text-white shadow-lg shadow-red-900/20 animate-pulse">
            <p class="text-[9px] font-black uppercase tracking-widest mb-2">Impersonation Active</p>
            <a href="{{ route('agency.impersonate.stop') }}" class="w-full h-10 bg-white text-red-600 rounded-lg flex items-center justify-center text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">
                Exit Protocol
            </a>
        </div>
    @endif

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground transition-colors">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Log Out
        </button>
    </form>
</div>
