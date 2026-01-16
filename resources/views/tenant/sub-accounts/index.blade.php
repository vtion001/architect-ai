{{--
    Sub-Accounts Index Page
    
    Sub-account/workspace management for agency tenants.
    
    Required variables:
    - $subAccounts: Collection of sub-account tenants
    - $capacity: Object with current, max, label properties
    
    Features:
    - Sub-account grid with telemetry
    - Impersonation with audit logging
    - Provisioning modal for new nodes
    - Capacity quota indicator
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showAddModal: false,
    showImpersonateModal: false,
    selectedSub: null,
    impersonateReason: 'Routine workspace management',
    name: '',
    slug: '',
    adminEmail: '',
    isProvisioning: false,
    isEntering: false,
    capacity: @js($capacity),

    provisionSubAccount() {
        if (!this.name || !this.slug || !this.adminEmail) {
            alert('All fields are required.');
            return;
        }
        this.isProvisioning = true;
        fetch('{{ route('agency.sub-accounts.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ name: this.name, slug: this.slug, admin_email: this.adminEmail })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Provisioning failed.');
            }
        })
        .finally(() => { this.isProvisioning = false; });
    },

    enterSession() {
        if (!this.impersonateReason || this.impersonateReason.length < 10) {
            alert('A valid session goal (min 10 chars) is required.');
            return;
        }
        this.isEntering = true;
        fetch('{{ route('agency.impersonate') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ tenant_id: this.selectedSub.id, reason: this.impersonateReason })
        })
        .then(res => res.json())
        .then(data => {
            window.location.href = data.redirect;
        })
        .finally(() => { this.isEntering = false; });
    }
}">
    {{-- Page Header --}}
    @include('tenant.sub-accounts.partials.header')

    {{-- Sub-Account Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($subAccounts as $sub)
            @include('tenant.sub-accounts.partials.account-card', ['sub' => $sub])
        @empty
            <div class="col-span-full py-20 text-center opacity-50">
                <i data-lucide="layers" class="w-16 h-16 mx-auto mb-4 text-slate-400"></i>
                <p class="text-sm font-medium italic">No sub-accounts provisioned yet.</p>
            </div>
        @endforelse
    </div>

    {{-- Impersonation Modal --}}
    @include('tenant.sub-accounts.partials.impersonate-modal')

    {{-- Provisioning Modal --}}
    @include('tenant.sub-accounts.partials.add-modal')
</div>
@endsection