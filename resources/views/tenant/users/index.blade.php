{{--
    Users Management Index Page
    
    Identity management interface for authorized personnel.
    
    Required variables:
    - $users: Collection of tenant users
    - $invitations: Collection of pending invitations
    - $roles: Available roles for assignment
    - $stats: Array with total_identities, active_sessions, security_health
    
    Features:
    - User list with status indicators
    - Pending invitations sidebar
    - Invite modal for new users
    - Stats dashboard
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showInviteModal: false,
    inviteEmail: '',
    inviteRoleId: '',
    isInviting: false,

    sendInvite() {
        if (!this.inviteEmail || !this.inviteRoleId) {
            alert('Identity and Role are mandatory.');
            return;
        }
        this.isInviting = true;
        fetch('{{ route('users.invite') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: this.inviteEmail,
                role_id: this.inviteRoleId
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                window.location.reload();
            } else {
                alert(data.message || 'Identity provisioning failed.');
                this.isInviting = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isInviting = false;
        });
    }
}">
    {{-- Page Header --}}
    @include('tenant.users.partials.header')

    {{-- Identity Telemetry Stats --}}
    @include('tenant.users.partials.stats')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- Active Members List --}}
        @include('tenant.users.partials.users-list')

        {{-- Pending Invitations Sidebar --}}
        @include('tenant.users.partials.pending-invitations')
    </div>

    {{-- Invite Modal --}}
    @include('tenant.users.partials.invite-modal')
</div>
@endsection
