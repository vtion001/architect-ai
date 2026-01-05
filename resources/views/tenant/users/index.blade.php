@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    activeTab: 'members',
    showAddModal: false,
    email: '',
    role_id: '',
    isAdding: false,

    inviteUser() {
        this.isAdding = true;
        fetch('{{ route('users.invite') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: this.email,
                role_id: this.role_id
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to dispatch invitation');
                this.isAdding = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isAdding = false;
        });
    },

    copyInvite(token) {
        const link = `{{ url('/auth/join/') }}/${token}`;
        navigator.clipboard.writeText(link);
        alert('Invitation link copied to clipboard.');
    }
}">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2 uppercase tracking-tighter">Identity Management</h1>
            <p class="text-muted-foreground font-medium italic">Provision and manage access protocols for this workspace.</p>
        </div>
        <button @click="showAddModal = true" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Invite New Identity
        </button>
    </div>

    <!-- Tab Toggles -->
    <div class="flex gap-4 mb-6 border-b border-border">
        <button @click="activeTab = 'members'" :class="activeTab === 'members' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground'" class="pb-3 px-2 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all">Active Members</button>
        <button @click="activeTab = 'invites'" :class="activeTab === 'invites' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground'" class="pb-3 px-2 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2">
            Pending Protocol
            <span class="bg-primary/10 text-primary px-1.5 py-0.5 rounded-md text-[9px]">{{ count($invitations) }}</span>
        </button>
    </div>

    <!-- Members Table -->
    <div x-show="activeTab === 'members'" class="bg-card border border-border rounded-3xl overflow-hidden shadow-sm animate-in fade-in duration-300">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-muted/30 text-muted-foreground font-black uppercase tracking-widest border-b border-border">
                <tr>
                    <th class="p-4 px-8">Identity</th>
                    <th class="p-4">Role / Scope</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Last Active</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                @foreach($users as $user)
                    <tr class="hover:bg-muted/10 transition-colors">
                        <td class="p-4 px-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border border-primary/10 font-black text-primary shadow-inner">
                                    {{ substr($user->email, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-foreground leading-tight">{{ $user->email }}</p>
                                    <p class="text-[10px] text-muted-foreground font-mono uppercase">{{ substr($user->id, 0, 8) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            @foreach($user->roles as $role)
                                <span class="px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest border border-primary/20">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->status === 'active' ? 'text-green-600 bg-green-50 border border-green-100' : 'text-amber-600 bg-amber-50 border border-amber-100' }}">
                                <span class="w-1 h-1 rounded-full {{ $user->status === 'active' ? 'bg-green-600' : 'bg-amber-600' }}"></span>
                                {{ $user->status }}
                            </span>
                        </td>
                        <td class="p-4 text-muted-foreground text-[10px] font-bold uppercase">
                            {{ $user->last_login_at?->diffForHumans() ?? 'NEVER' }}
                        </td>
                        <td class="p-4 text-right">
                            <button class="p-2 text-muted-foreground hover:text-primary hover:bg-primary/5 rounded-lg transition-all">
                                <i data-lucide="shield-alert" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Invites Section -->
    <div x-show="activeTab === 'invites'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-in fade-in duration-300">
        @forelse($invitations as $invite)
            <div class="bg-card border border-border rounded-3xl p-6 shadow-sm hover:border-primary/30 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-muted flex items-center justify-center">
                        <i data-lucide="mail" class="w-5 h-5 text-muted-foreground"></i>
                    </div>
                    <span class="px-2 py-1 rounded-lg bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest border border-amber-100">Pending Identity</span>
                </div>
                <h3 class="font-bold text-sm mb-1">{{ $invite->email }}</h3>
                <p class="text-[10px] text-muted-foreground font-bold uppercase tracking-widest mb-4">Role: {{ $invite->role->name }}</p>
                
                <div class="pt-4 border-t border-border/50 flex gap-2">
                    <button @click="copyInvite('{{ $invite->token }}')" class="flex-1 h-9 bg-muted border border-border rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-primary hover:text-white hover:border-primary transition-all flex items-center justify-center gap-2">
                        <i data-lucide="link" class="w-3 h-3"></i>
                        Copy Link
                    </button>
                    <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center text-muted-foreground opacity-50 italic">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4"></i>
                <p>No pending identity protocols in the queue.</p>
            </div>
        @endforelse
    </div>

    <!-- Invite User Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="!isAdding && (showAddModal = false)" class="bg-card w-full max-w-md rounded-3xl shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Invite Identity</h2>
            <p class="text-sm text-muted-foreground mb-8 italic">Initiate an onboarding protocol for a new workspace member.</p>
            
            <form @submit.prevent="inviteUser" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Target Email Address</label>
                    <input x-model="email" type="email" required placeholder="member@company.com"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Permissions Level (Role)</label>
                    <select x-model="role_id" required class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="">Choose access level...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex flex-col gap-3">
                    <button type="submit" :disabled="isAdding" class="w-full h-14 bg-primary text-primary-foreground font-black uppercase tracking-[0.2em] text-xs shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                        <template x-if="isAdding">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isAdding ? 'DISPATCHING...' : 'DISPATCH INVITATION'"></span>
                    </button>
                    <button type="button" @click="showAddModal = false" :disabled="isAdding" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection