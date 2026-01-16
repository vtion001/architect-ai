{{--
    Admin Tenant Show Page
    
    Detailed tenant intelligence view for admin oversight.
    
    Required variables:
    - $tenant: Tenant model with users and subAccounts relationships
    - $tokenBalance: Current token balance
    - $transactions: Collection of recent transactions
    - $linkedWaitlist: Optional linked waitlist entry
    
    Features:
    - Tenant DNA and status display
    - Resource economy with grants
    - User impersonation capability
    - Sub-accounts hierarchy view
--}}

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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ user_id: this.selectedUser.id, reason: this.justification })
        })
        .then(res => res.json())
        .then(data => { window.location.href = data.redirect; })
        .catch(e => { console.error(e); this.isImpersonating = false; });
    }
}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Sidebar: Tenant Details --}}
        @include('admin.tenants.partials.sidebar')

        {{-- Grant Resources Modal --}}
        @include('admin.tenants.partials.grant-modal')

        {{-- Main: Users & Hierarchy --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Users Table --}}
            @include('admin.tenants.partials.users-table')

            {{-- Sub-Accounts --}}
            @include('admin.tenants.partials.sub-accounts')
        </div>
    </div>

    {{-- Impersonation Modal --}}
    @include('admin.tenants.partials.impersonate-modal')
</div>
@endsection
