@extends('layouts.admin')

@section('title', 'Global Grid Master Registry')

@section('content')
<div class="space-y-12 animate-in fade-in duration-700">
    <!-- Global Telemetry Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/5 blur-3xl rounded-full group-hover:bg-emerald-500/10 transition-colors"></div>
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-black transition-all">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Integrity</span>
            </div>
            <p class="text-2xl font-black text-emerald-400 uppercase tracking-tighter">Verified</p>
            <p class="text-[10px] font-bold text-slate-500 uppercase mt-2 italic">Grid Status: 99.99%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Global Protocol Registry -->
        <div class="lg:col-span-8 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Cross-Grid Protocol Registry</h3>
            <div class="bg-slate-900 border border-slate-800 rounded-[40px] overflow-hidden shadow-2xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-950/50 text-slate-500 font-black uppercase tracking-widest border-b border-slate-800">
                        <tr>
                            <th class="p-6">Origin Node</th>
                            <th class="p-6">Identity</th>
                            <th class="p-6">Protocol Action</th>
                            <th class="p-6 text-right">Registry Cycle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($globalAudit as $log)
                            <tr class="hover:bg-slate-800/30 transition-colors group">
                                <td class="p-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-300 uppercase tracking-tight">{{ $log->tenant?->name ?? 'GRID_CORE' }}</span>
                                        <span class="text-[8px] font-mono text-slate-600 uppercase">{{ substr($log->tenant_id ?? 'root', 0, 13) }}</span>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="text-slate-400 font-medium">{{ $log->actor?->email ?? 'SYSTEM' }}</span>
                                </td>
                                <td class="p-6 font-mono text-blue-400 font-bold tracking-tighter uppercase">
                                    {{ $log->action }}
                                </td>
                                <td class="p-6 text-right text-slate-500 font-medium italic">
                                    {{ $log->timestamp->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Beta Lead Hub -->
        <div class="lg:col-span-4 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 text-cyan-500">Master Lead Provisioning</h3>
            <div class="bg-slate-900 border border-slate-800 rounded-[40px] p-8 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-cyan-400/5 rounded-full blur-3xl"></div>
                
                @forelse($waitlistEntries as $entry)
                    <div class="p-5 rounded-3xl bg-slate-950/50 border border-slate-800 hover:border-cyan-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 font-black">
                                {{ substr($entry->email, 0, 1) }}
                            </div>
                            <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded border {{ $entry->status === 'pending' ? 'text-amber-500 border-amber-500/20' : 'text-green-500 border-green-500/20' }}">
                                {{ $entry->status }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs font-bold text-white truncate">{{ $entry->email }}</p>
                            <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mt-1">{{ $entry->agency_name ?? 'Individual Node' }}</p>
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="flex-1 h-9 bg-cyan-500 text-black rounded-xl font-black uppercase text-[8px] tracking-widest hover:bg-white transition-all">Provision</button>
                            <button class="w-9 h-9 bg-slate-800 text-slate-400 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center opacity-30 italic">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4"></i>
                        <p class="text-xs font-bold uppercase tracking-widest">Lead queue empty</p>
                    </div>
                @endforelse

                <a href="/admin/tenants" class="w-full h-14 bg-slate-800 border border-slate-700 rounded-2xl font-black uppercase text-[9px] tracking-widest hover:bg-slate-700 text-white transition-all flex items-center justify-center gap-3">
                    Grid Explorer
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection