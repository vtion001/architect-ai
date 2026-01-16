{{-- God View - Global Telemetry Matrix --}}
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
    <div class="bg-slate-900 border border-slate-800 rounded-[32px] p-8 relative overflow-hidden group shadow-xl">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/5 blur-3xl rounded-full group-hover:bg-blue-500/10 transition-colors"></div>
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 group-hover:bg-blue-500 group-hover:text-white transition-all">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Nodes</span>
        </div>
        <p class="text-4xl font-black text-white">{{ $statistics['total_tenants'] }}</p>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Provisioned Tenants</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-[32px] p-8 relative overflow-hidden group shadow-xl">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-purple-500/5 blur-3xl rounded-full group-hover:bg-purple-500/10 transition-colors"></div>
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500 border border-purple-500/20 group-hover:bg-purple-500 group-hover:text-white transition-all">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Identities</span>
        </div>
        <p class="text-4xl font-black text-white">{{ $statistics['total_identities'] }}</p>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Active Verified Blocks</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-[32px] p-8 relative overflow-hidden group shadow-xl">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-cyan-500/5 blur-3xl rounded-full group-hover:bg-cyan-500/10 transition-colors"></div>
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 border border-cyan-500/20 group-hover:bg-cyan-500 group-hover:text-black transition-all">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Credits</span>
        </div>
        <p class="text-4xl font-black text-cyan-400">{{ number_format($statistics['global_credits']) }}</p>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Global Token Hashing</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-[32px] p-8 relative overflow-hidden group shadow-xl">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/5 blur-3xl rounded-full group-hover:bg-amber-500/10 transition-colors"></div>
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20 group-hover:bg-amber-500 group-hover:text-white transition-all">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Growth</span>
        </div>
        <div class="flex items-end gap-2">
            <p class="text-4xl font-black text-amber-400">{{ $statistics['total_waitlist'] }}</p>
            <div class="mb-1 flex flex-col">
                <span class="text-[8px] font-black text-green-500 uppercase leading-none">+{{ $statistics['signups_today'] }} Today</span>
                <span class="text-[8px] font-bold text-slate-500 uppercase leading-none mt-1">+{{ $statistics['signups_this_week'] }} week</span>
            </div>
        </div>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">{{ $statistics['active_waitlist'] }} Pending Approval</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-[32px] p-8 relative overflow-hidden group shadow-xl">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/5 blur-3xl rounded-full group-hover:bg-emerald-500/10 transition-colors"></div>
        <div class="flex items-center justify-between mb-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Integrity</span>
        </div>
        <div class="flex items-end gap-2">
            <p class="text-4xl font-black text-emerald-400">99.9%</p>
            <span class="text-[10px] font-black text-green-500 uppercase mb-1">Health</span>
        </div>
        <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Global MFA Sync: Verified</p>
    </div>
</div>
