@extends('layouts.admin')

@section('title', 'Platform Operations')

@section('content')
<!-- Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" x-data="{
    isToggling: false,
    justification: '',
    showConfirm: false,
    targetState: {{ session('developer_observability_mode') ? 'false' : 'true' }},

    toggle() {
        if (!this.justification) {
            alert('Justification is required for this action.');
            return;
        }
        this.isToggling = true;
        fetch('{{ route('admin.toggle-observability') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                enabled: this.targetState,
                justification: this.justification
            })
        })
        .then(res => res.json())
        .then(data => {
            window.location.reload();
        })
        .catch(e => {
            console.error(e);
            this.isToggling = false;
        });
    }
}">
    <!-- Tenants -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <i data-lucide="building" class="w-5 h-5 text-blue-500"></i>
            </div>
            <span class="text-[10px] font-black text-slate-500 uppercase">Live Tenants</span>
        </div>
        <p class="text-3xl font-black text-white">{{ $stats['total_tenants'] }}</p>
    </div>

    <!-- Users -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5 text-purple-500"></i>
            </div>
            <span class="text-[10px] font-black text-slate-500 uppercase">Total Users</span>
        </div>
        <p class="text-3xl font-black text-white">{{ $stats['total_users'] }}</p>
    </div>

    <!-- Waitlist -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center">
                <i data-lucide="user-plus" class="w-5 h-5 text-cyan-500"></i>
            </div>
            <span class="text-[10px] font-black text-slate-500 uppercase">Waitlist Leads</span>
        </div>
        <p class="text-3xl font-black text-white">{{ $stats['waitlist_count'] }}</p>
    </div>

    <!-- Observability -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm flex items-center justify-between relative overflow-hidden group">
        <div class="relative z-10">
            <p class="text-[10px] font-black text-slate-500 uppercase mb-1 tracking-widest">Break Glass</p>
            <h3 class="text-lg font-bold text-white uppercase">{{ session('developer_observability_mode') ? 'Active' : 'Inactive' }}</h3>
        </div>
        <button @click="showConfirm = true" class="w-10 h-10 bg-slate-800 border border-slate-700 rounded-xl flex items-center justify-center text-slate-400 group-hover:text-red-500 transition-all">
            <i data-lucide="eye" class="w-5 h-5"></i>
        </button>

        <!-- Confirm Modal -->
        <div x-show="showConfirm" x-cloak class="absolute inset-0 bg-slate-900/95 backdrop-blur-sm z-20 flex flex-col p-4 animate-in fade-in zoom-in-95 duration-200">
            <textarea x-model="justification" class="flex-1 bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs font-medium text-slate-300 outline-none focus:ring-1 focus:ring-red-600 mb-2" placeholder="Mandatory Justification..."></textarea>
            <div class="flex gap-2">
                <button @click="showConfirm = false" class="flex-1 h-8 rounded-lg bg-slate-800 text-[9px] font-black uppercase text-slate-400">Abort</button>
                <button @click="toggle" :disabled="isToggling || justification.length < 10" class="flex-1 h-8 rounded-lg bg-red-600 text-[9px] font-black uppercase text-white">Switch State</button>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Audit Feed -->
    <div class="lg:col-span-2 space-y-6">
        <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.2em] px-1">Global Audit Pulse</h3>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/50 text-slate-500 font-black uppercase tracking-widest border-b border-slate-800">
                    <tr>
                        <th class="p-4 px-6">Identity</th>
                        <th class="p-4">Tenant</th>
                        <th class="p-4">Action</th>
                        <th class="p-4">Result</th>
                        <th class="p-4 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($recentLogs as $log)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="p-4 px-6">
                                <p class="font-bold text-slate-300">{{ $log->actor?->email ?? 'SYSTEM' }}</p>
                                <p class="text-[9px] font-mono text-slate-600 uppercase">{{ substr($log->actor_id ?? 'root', 0, 8) }}</p>
                            </td>
                            <td class="p-4 font-mono text-slate-400">{{ $log->tenant?->name ?? 'GRID' }}</td>
                            <td class="p-4 font-bold uppercase text-slate-200 tracking-tight">{{ $log->action }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-full font-black text-[9px] uppercase tracking-widest {{ $log->result === 'success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                                    {{ $log->result }}
                                </span>
                            </td>
                            <td class="p-4 text-right text-slate-500">{{ $log->timestamp->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Waitlist Leads -->
    <div class="space-y-6" x-data="{
        isConverting: false,
        convertLead(id) {
            if (!confirm('Provision new tenant from this lead?')) return;
            this.isConverting = true;
            fetch(`/admin/waitlist/${id}/convert`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message + '\n\nInvitation Link: ' + data.invitation_url);
                    window.location.reload();
                }
            })
            .finally(() => { this.isConverting = false; });
        }
    }">
        <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.2em] px-1 text-cyan-500">Beta Protocol Queue</h3>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-sm">
            @forelse($waitlistLeads as $lead)
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-950/50 border border-slate-800 group hover:border-cyan-500/30 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 font-black">
                        {{ substr($lead->email, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ $lead->email }}</p>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $lead->agency_name ?? 'Individual' }}</p>
                    </div>
                    @if($lead->status === 'pending')
                        <button @click="convertLead('{{ $lead->id }}')" :disabled="isConverting" class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-cyan-500 hover:bg-cyan-500/10 rounded-lg disabled:opacity-50">
                            <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                        </button>
                    @else
                        <span class="text-[8px] font-black uppercase text-slate-600">Invited</span>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center text-slate-600 italic">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                    <p class="text-xs">Queue is empty.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection