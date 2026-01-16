{{-- Users Management - Pending Invitations --}}
<div class="lg:col-span-4 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 text-primary">Pending Provisioning</h3>
    <div class="bg-card border border-border rounded-[40px] p-8 space-y-6 shadow-sm">
        @forelse($invitations as $invite)
            @include('tenant.users.partials.invitation-card', ['invite' => $invite])
        @empty
            <div class="py-12 text-center text-slate-500 italic">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                <p class="text-xs">No pending identities found.</p>
            </div>
        @endforelse
    </div>
</div>
