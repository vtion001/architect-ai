@extends('layouts.admin')

@section('title', 'Global Audit Analyzer')

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
        <form action="{{ route('admin.audit.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Action (e.g. content.create)" 
                   class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-300 outline-none focus:ring-1 focus:ring-red-600">
            
            <input type="text" name="tenant_id" value="{{ request('tenant_id') }}" placeholder="Tenant UUID" 
                   class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-300 outline-none focus:ring-1 focus:ring-red-600">

            <select name="actor_type" class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-300 outline-none focus:ring-1 focus:ring-red-600">
                <option value="">All Actors</option>
                <option value="user" {{ request('actor_type') === 'user' ? 'selected' : '' }}>Users</option>
                <option value="developer" {{ request('actor_type') === 'developer' ? 'selected' : '' }}>Developers</option>
                <option value="system" {{ request('actor_type') === 'system' ? 'selected' : '' }}>System</option>
            </select>

            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-all">
                Analyze Logs
            </button>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-950/50 text-slate-500 font-black uppercase tracking-widest border-b border-slate-800">
                <tr>
                    <th class="p-4 px-6">Timestamp</th>
                    <th class="p-4">Actor</th>
                    <th class="p-4">Tenant</th>
                    <th class="p-4">Action</th>
                    <th class="p-4">Result</th>
                    <th class="p-4">Context / Justification</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($logs as $log)
                    <tr class="hover:bg-slate-800/30 transition-colors {{ $log->actor_type === 'developer' ? 'bg-red-900/5' : '' }}">
                        <td class="p-4 px-6 text-slate-500 font-medium">
                            {{ $log->timestamp->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full {{ $log->actor_type === 'developer' ? 'bg-red-600' : 'bg-slate-700' }} flex items-center justify-center text-[8px] font-black text-white uppercase">
                                    {{ substr($log->actor_type, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-200">{{ $log->actor?->email ?? 'System' }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-tighter">{{ $log->actor_type }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <p class="font-medium text-slate-400">{{ $log->tenant?->name ?? 'Global' }}</p>
                            <p class="text-[9px] text-slate-600 font-mono">{{ $log->tenant_id }}</p>
                        </td>
                        <td class="p-4 font-mono text-blue-400">
                            {{ $log->action }}
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $log->result === 'success' ? 'bg-green-500/10 text-green-500 border-green-500/20' : ($log->result === 'denied' ? 'bg-red-500/10 text-red-500 border-red-500/20' : 'bg-slate-500/10 text-slate-500 border-slate-500/20') }}">
                                {{ $log->result }}
                            </span>
                        </td>
                        <td class="p-4 max-w-xs">
                            @if($log->justification)
                                <p class="text-red-400 font-medium italic">"{{ $log->justification }}"</p>
                            @endif
                            @if($log->metadata)
                                <p class="text-slate-500 truncate" title="{{ json_encode($log->metadata) }}">{{ json_encode($log->metadata) }}</p>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
