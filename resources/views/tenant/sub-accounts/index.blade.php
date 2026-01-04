@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showCreateModal: false,
    name: '',
    slug: '',
    admin_email: '',
    isCreating: false,

    createSubAccount() {
        this.isCreating = true;
        fetch('{{ route('sub-accounts.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: this.name,
                slug: this.slug,
                admin_email: this.admin_email
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                window.location.reload();
            } else {
                alert(data.message || 'Creation failed');
                this.isCreating = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isCreating = false;
        });
    }
}">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Sub-Account Management</h1>
            <p class="text-muted-foreground">Manage your nested clients and business units.</p>
        </div>
        <button @click="showCreateModal = true" class="bg-primary text-primary-foreground px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-primary/20 flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            New Sub-Account
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($subAccounts as $account)
            <div class="bg-card border border-border rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <i data-lucide="building" class="w-5 h-5 text-primary"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 bg-green-50 text-green-600 rounded-md border border-green-100">
                        {{ $account->status }}
                    </span>
                </div>
                <h3 class="text-lg font-bold mb-1">{{ $account->name }}</h3>
                <p class="text-xs text-muted-foreground mb-4">/{{ $account->slug }}</p>
                
                <div class="flex items-center gap-4 text-xs font-medium text-muted-foreground">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        {{ $account->users_count }} Users
                    </span>
                </div>

                <div class="mt-6 pt-6 border-t border-border flex gap-2">
                    <button class="flex-1 py-2 text-xs font-bold uppercase tracking-widest border border-border rounded-lg hover:bg-muted transition-colors">Manage</button>
                    <a href="/login/{{ $account->slug }}" class="flex-1 py-2 text-xs font-bold uppercase tracking-widest bg-muted text-foreground text-center rounded-lg hover:bg-muted/80 transition-colors">Login</a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="showCreateModal = false" class="bg-card w-full max-w-md rounded-3xl shadow-2xl border border-border p-8">
            <h2 class="text-2xl font-bold mb-6">New Sub-Account</h2>
            
            <form @submit.prevent="createSubAccount" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Name</label>
                    <input x-model="name" type="text" required
                           @input="slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')"
                           class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Slug</label>
                    <input x-model="slug" type="text" required
                           class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Admin Email</label>
                    <input x-model="admin_email" type="email" required
                           class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showCreateModal = false" class="flex-1 h-12 rounded-xl border border-border font-bold uppercase text-xs">Cancel</button>
                    <button type="submit" :disabled="isCreating" class="flex-1 h-12 rounded-xl bg-primary text-primary-foreground font-bold uppercase text-xs shadow-lg shadow-primary/20">
                        <span x-text="isCreating ? 'Provisioning...' : 'Create Workspace'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
