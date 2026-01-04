@extends('layouts.admin')

@section('title', 'Operations Dashboard')

@section('content')
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
    <!-- Stats Cards -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <i data-lucide="building" class="w-5 h-5 text-blue-500"></i>
            </div>
            <span class="text-[10px] font-black text-slate-500 uppercase">Live Tenants</span>
        </div>
        <p class="text-3xl font-black text-white">{{ $stats['total_tenants'] }}</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5 text-purple-500"></i>
            </div>
            <span class="text-[10px] font-black text-slate-500 uppercase">System Users</span>
        </div>
        <p class="text-3xl font-black text-white">{{ $stats['total_users'] }}</p>
    </div>

    <!-- Observability Toggle Card -->
    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm flex items-center justify-between overflow-hidden relative">
        <div class="relative z-10">
            <h3 class="text-lg font-bold text-white mb-1">Global Observability</h3>
            <p class="text-xs text-slate-400 max-w-xs leading-relaxed">Toggle the "Break Glass" mode to bypass tenant isolation scopes for cross-system debugging.</p>
        </div>
        
        <div class="relative z-10 flex flex-col items-end gap-3">
            <div @click="showConfirm = true" 
                 class="w-14 h-7 rounded-full bg-slate-800 border border-slate-700 cursor-pointer transition-all relative overflow-hidden"
                 :class="targetState ? '' : 'bg-red-600/20 border-red-500/30'">
                <div class="absolute top-1 left-1 w-5 h-5 rounded-full shadow-md transition-all duration-300"
                     :class="targetState ? 'bg-slate-600' : 'bg-red-600 translate-x-7'"></div>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest" :class="targetState ? 'text-slate-500' : 'text-red-500'">{{ session('developer_observability_mode') ? 'Active' : 'Inactive' }}</span>
        </div>

        <!-- Confirm Modal -->
        <div x-show="showConfirm" x-cloak class="absolute inset-0 bg-slate-900/95 backdrop-blur-sm z-20 flex flex-col p-6 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-black text-white uppercase tracking-wider">Action Justification</h4>
                <button @click="showConfirm = false" class="text-slate-500 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            <textarea x-model="justification" class="flex-1 bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs font-medium text-slate-300 outline-none focus:ring-1 focus:ring-red-600 transition-all mb-4" placeholder="Required: Why are you toggling global system visibility?"></textarea>
            <button @click="toggle" :disabled="isToggling || justification.length < 10" class="w-full h-10 rounded-xl bg-red-600 text-white text-xs font-bold uppercase tracking-widest hover:bg-red-700 transition-all disabled:opacity-50">
                <span x-show="!isToggling">Confirm State Change</span>
                <span x-show="isToggling">Updating System...</span>
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.2em]">Platform Pulse</h3>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 min-h-[400px] flex items-center justify-center">
            <div class="text-center text-slate-600">
                <i data-lucide="activity" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                <p class="text-sm font-medium">Real-time system telemetry placeholder.</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.2em]">Active Developer Sessions</h3>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex items-center gap-4 p-3 rounded-2xl bg-slate-950/50 border border-slate-800/50">
                <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center text-white text-xs font-black shadow-lg">
                    {{ substr(auth()->user()->email, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->email }}</p>
                    <p class="text-[10px] text-green-500 font-bold uppercase tracking-widest">Active Now</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
