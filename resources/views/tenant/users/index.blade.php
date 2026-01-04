@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showAddModal: false,
    email: '',
    role_id: '',
    isAdding: false,

    addUser() {
        this.isAdding = true;
        fetch('{{ route('users.store') }}', {
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
                alert(data.message || 'Failed to add user');
                this.isAdding = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isAdding = false;
        });
    }
}">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Team Management</h1>
            <p class="text-muted-foreground font-medium">Provision and manage user identities for this workspace.</p>
        </div>
        <button @click="showAddModal = true" class="bg-primary text-primary-foreground px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Add Team Member
        </button>
    </div>

    <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-muted/30 text-muted-foreground font-black uppercase tracking-widest border-b border-border">
                <tr>
                    <th class="p-4 px-8">Identity</th>
                    <th class="p-4">Role / Scope</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Last Active</th>
                    <th class="p-4">Actions</th>
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
                                    <p class="text-[10px] text-muted-foreground font-mono">{{ $user->id }}</p>
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
                        <td class="p-4 text-muted-foreground text-xs font-medium">
                            {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                        </td>
                        <td class="p-4">
                            <button class="p-2 text-muted-foreground hover:text-primary hover:bg-primary/5 rounded-lg transition-all">
                                <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="!isAdding && (showAddModal = false)" class="bg-card w-full max-w-md rounded-3xl shadow-2xl border border-border p-8 animate-in zoom-in-95 duration-200">
            <h2 class="text-2xl font-bold mb-2">New Team Member</h2>
            <p class="text-sm text-muted-foreground mb-8">Grant workspace access to a new identity.</p>
            
            <form @submit.prevent="addUser" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Email Address</label>
                    <input x-model="email" type="email" required placeholder="member@company.com"
                           class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Assigned Role</label>
                    <select x-model="role_id" required class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="">Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showAddModal = false" :disabled="isAdding" class="flex-1 h-12 rounded-xl border border-border font-bold uppercase text-xs hover:bg-muted transition-all">Cancel</button>
                    <button type="submit" :disabled="isAdding" class="flex-1 h-12 rounded-xl bg-primary text-primary-foreground font-bold uppercase text-xs shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                        <template x-if="isAdding">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        </template>
                        <span x-text="isAdding ? 'Adding...' : 'Add Member'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
