{{--
    Admin God View Dashboard
    
    Global Grid Master Registry for super admin oversight.
    
    Required variables:
    - $statistics: Global platform stats (tenants, identities, credits, waitlist)
    - $llmHealth: Optional LLMOps health metrics
    - $globalAudit: Collection of recent audit log entries
    - $waitlistEntries: Collection of pending waitlist entries
    
    Features:
    - Global telemetry metrics
    - LLMOps system vitality monitor
    - Protocol registry (audit log)
    - Lead provisioning hub
    - Master waitlist registry
--}}

@extends('layouts.admin')

@section('title', 'Global Grid Master Registry')

@section('content')
<div class="space-y-12 animate-in fade-in duration-700">
    {{-- Global Telemetry Matrix --}}
    @include('admin.partials.god-view.telemetry')

    {{-- LLMOps System Vitality --}}
    @include('admin.partials.god-view.llmops')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Global Protocol Registry --}}
        @include('admin.partials.god-view.protocol-registry')

        {{-- Beta Lead Hub --}}
        @include('admin.partials.god-view.lead-hub')
    </div>

    {{-- Section Divider --}}
    <div class="h-px bg-gradient-to-r from-transparent via-slate-800 to-transparent"></div>

    {{-- Master Waitlist Registry --}}
    @include('admin.partials.god-view.waitlist-table')
</div>
@endsection