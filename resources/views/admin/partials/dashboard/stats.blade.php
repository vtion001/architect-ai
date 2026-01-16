{{-- Admin Dashboard - Stats Cards --}}
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
