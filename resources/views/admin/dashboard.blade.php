{{--
    Admin Dashboard
    
    Platform operations overview for admin users.
    
    Required variables:
    - $stats: Array with total_tenants, total_users, waitlist_count
    - $recentLogs: Collection of recent audit log entries
    - $waitlistLeads: Collection of pending waitlist entries
    
    Features:
    - Key metrics display
    - Break-glass observability toggle
    - Audit log feed
    - Waitlist queue with conversion
--}}

@extends('layouts.admin')

@section('title', 'Platform Operations')

@section('content')
{{-- Stats Row --}}
@include('admin.partials.dashboard.stats')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Audit Feed --}}
    @include('admin.partials.dashboard.audit-log')

    {{-- Waitlist Leads --}}
    @include('admin.partials.dashboard.waitlist')
</div>
@endsection