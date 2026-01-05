@extends('layouts.admin')

@section('title', 'Tenant Intelligence: ' . $tenant->name)

@section('content')
<div class="space-y-8" x-data="{
    showImpersonateModal: false,
    showGrantModal: false,
    selectedUser: null,
    justification: '',
    isImpersonating: false,
    isGranting: false,
    grantAmount: 1000,
    grantReason: 'Beta Resource Allocation',

    grantTokens() {
        if (!this.grantAmount || !this.grantReason) return;
        this.isGranting = true;
        fetch('{{ route('admin.tenants.grant', $tenant->id) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ amount: this.grantAmount, reason: this.grantReason })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            window.location.reload();
        })
        .finally(() => { this.isGranting = false; });
    },

    impersonate() {
        if (!this.justification || this.justification.length < 10) {
            alert('A valid justification (min 10 chars) is required.');
            return;
        }
        this.isImpersonating = true;
        fetch('{{ route('developer.impersonate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: this.selectedUser.id,
                reason: this.justification
            })
        })
        .then(res => res.json())
        .then(data => {
            window.location.href = data.redirect;
        })
        .catch(e => {
            console.error(e);
            this.isImpersonating = false;
        });
    }
}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Details -->
        <div class="space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Tenant DNA</h3>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6">
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Status</p>
                    <span class="px-2 py-0.5 rounded-full bg-green-500/10 text-green-500 text-[10px] font-bold border border-green-500/20">{{ $tenant->status }}</span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Type</p>
                    <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-500 text-[10px] font-bold border border-blue-500/20">{{ strtoupper($tenant->type) }}</span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Unique ID</p>
                    <p class="text-xs font-mono text-white">{{ $tenant->id }}</p>
                </div>
            </div>

            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Resource Economy</h3>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Current Balance</p>
                        <p class="text-3xl font-black text-cyan-500">{{ number_format($tokenBalance) }}</p>
                    </div>
                    <button @click="showGrantModal = true" class="w-10 h-10 bg-cyan-500/10 border border-cyan-500/20 rounded-xl flex items-center justify-center text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-slate-500 uppercase">Recent Transactions</p>
                    <div class="space-y-2 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($transactions as $tx)
                            <div class="p-2.5 rounded-xl bg-slate-950/50 border border-slate-800">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[9px] font-bold {{ $tx->amount > 0 ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                                    </span>
                                    <span class="text-[8px] text-slate-600 uppercase">{{ $tx->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium truncate">{{ $tx->reason }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    <!-- Grant Resources Modal -->
    <div x-show="showGrantModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="!isGranting && (showGrantModal = false)" class="bg-slate-900 border border-slate-800 w-full max-w-sm rounded-3xl shadow-2xl p-10 animate-in zoom-in-95 duration-200">
            <h2 class="text-2xl font-black text-white mb-2 uppercase tracking-tighter">Provision Resources</h2>
            <p class="text-slate-400 text-sm mb-8 italic">Manually allocate tokens to this agency node.</p>

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Token Quantity</label>
                    <input type="number" x-model="grantAmount" class="w-full h-14 bg-slate-950 border border-slate-800 rounded-2xl px-5 text-xl font-black text-cyan-500 outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Allocation Reason</label>
                    <input type="text" x-model="grantReason" class="w-full h-14 bg-slate-950 border border-slate-800 rounded-2xl px-5 text-sm font-bold text-white outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
                </div>
                
                <div class="flex flex-col gap-3 pt-4">
                    <button @click="grantTokens" :disabled="isGranting" class="w-full h-16 bg-cyan-500 text-black font-black uppercase tracking-[0.2em] text-xs rounded-2xl shadow-lg shadow-cyan-900/40 hover:bg-white transition-all flex items-center justify-center gap-3">
                        <template x-if="isGranting">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isGranting ? 'DISPATCHING...' : 'AUTHORIZE GRANT'"></span>
                    </button>
                    <button @click="showGrantModal = false" :disabled="isGranting" class="w-full h-14 bg-slate-800 text-slate-400 font-bold uppercase tracking-widest text-[10px] rounded-2xl hover:bg-slate-700 transition-all">Abort</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Main: Users & Hierarchy -->
        <div class="lg:col-span-2 space-y-8">
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Account Identities</h3>
                <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/50 text-slate-500 font-black uppercase border-b border-slate-800">
                            <tr>
                                <th class="p-4 px-6">User</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Last Login</th>
                                <th class="p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @foreach($tenant->users as $user)
                                <tr>
                                    <td class="p-4 px-6">
                                        <p class="font-bold text-white">{{ $user->email }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $user->id }}</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-[10px] font-bold text-slate-400">{{ $user->status }}</span>
                                    </td>
                                    <td class="p-4 text-slate-500">
                                        {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                                    </td>
                                    <td class="p-4">
                                        <button @click="selectedUser = @js($user); showImpersonateModal = true" class="px-3 py-1.5 rounded-lg bg-red-600/10 text-red-500 hover:bg-red-600 hover:text-white text-[10px] font-black uppercase tracking-widest transition-all">
                                            Impersonate
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($tenant->subAccounts->isNotEmpty())
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Nested Child Accounts</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($tenant->subAccounts as $sub)
                        <a href="{{ route('admin.tenants.show', $sub) }}" class="p-4 rounded-2xl bg-slate-900 border border-slate-800 hover:border-blue-500/30 transition-all flex items-center justify-between">
                            <div>
                                <p class="font-bold text-white text-sm">{{ $sub->name }}</p>
                                <p class="text-[10px] text-slate-500">/{{ $sub->slug }}</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-700"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Impersonation Confirmation Modal -->
    <div x-show="showImpersonateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="!isImpersonating && (showImpersonateModal = false)" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-8 text-center animate-in fade-in zoom-in-95 duration-200">
            <div class="w-20 h-20 rounded-full bg-red-600/10 flex items-center justify-center mx-auto mb-6 border border-red-600/20">
                <i data-lucide="user-plus" class="w-10 h-10 text-red-600"></i>
            </div>
            <h2 class="text-2xl font-black text-white mb-2">Initiate Impersonation</h2>
            <p class="text-slate-400 text-sm mb-8 leading-relaxed">
                You are about to access the system as <span class="text-white font-bold" x-text="selectedUser?.email"></span>. 
                This action will be strictly audited under the IAM Break-Glass Protocol.
            </p>

            <div class="space-y-4">
                <textarea x-model="justification" class="w-full h-24 bg-slate-950 border border-slate-800 rounded-2xl p-4 text-xs text-slate-300 outline-none focus:ring-1 focus:ring-red-600 transition-all" placeholder="Required: Why do you need to impersonate this user? (min 10 chars)"></textarea>
                
                <div class="flex flex-col gap-3">
                    <button @click="impersonate" :disabled="isImpersonating || justification.length < 10" class="w-full py-4 rounded-2xl bg-red-600 text-white font-black uppercase tracking-widest text-xs shadow-lg shadow-red-900/40 hover:bg-red-700 disabled:opacity-50 transition-all">
                        <span x-show="!isImpersonating">Authorize & Enter Session</span>
                        <span x-show="isImpersonating">Provisioning Access...</span>
                    </button>
                    <button @click="showImpersonateModal = false" :disabled="isImpersonating" class="w-full py-4 rounded-2xl bg-slate-800 text-slate-400 font-bold uppercase tracking-widest text-xs hover:bg-slate-700 transition-all">
                        Abort Protocol
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
