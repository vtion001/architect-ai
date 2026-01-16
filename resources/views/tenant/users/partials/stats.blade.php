{{-- Users Management - Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm relative overflow-hidden group">
        <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
            <i data-lucide="shield" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Identities</p>
            <p class="text-2xl font-black text-white">{{ $stats['total_identities'] }}</p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
            <i data-lucide="shield" class="w-24 h-24 text-blue-500"></i>
        </div>
    </div>
    
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm relative overflow-hidden group">
        <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500">
            <i data-lucide="activity" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Active Sessions</p>
            <p class="text-2xl font-black text-white">{{ $stats['active_sessions'] }}</p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
            <i data-lucide="activity" class="w-24 h-24 text-green-500"></i>
        </div>
    </div>

    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm relative overflow-hidden group">
        <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500">
            <i data-lucide="lock" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Security Health</p>
            <p class="text-2xl font-black text-white">{{ $stats['security_health'] }}%</p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
            <i data-lucide="lock" class="w-24 h-24 text-purple-500"></i>
        </div>
    </div>
</div>
