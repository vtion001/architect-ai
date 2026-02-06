{{--
    Tenant Settings Index Page
    
    Grid configuration with tabbed navigation.
    Modularized - uses @include for partials.
    
    Required variables:
    - $activeTab: Current active tab
    - $tenant: Current tenant
    - $user: Current user
    - $tokenBalance: Token balance
    - $apiTokens: API tokens collection
    - $auditLogs: Audit logs collection
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{ 
    activeTab: '{{ $activeTab }}',
    brandColor: '{{ $tenant->metadata['primary_color'] ?? '#00F2FF' }}',
    tempColor: '{{ $tenant->metadata['primary_color'] ?? '#00F2FF' }}'
}">
    {{-- Page Header --}}
    @include('tenant.settings.partials.header', ['tokenBalance' => $tokenBalance])

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- Navigation Sidebar --}}
        @include('tenant.settings.partials.navigation')

        {{-- Configuration Panel --}}
        <div class="lg:col-span-9">
            {{-- API Protocols Tab --}}
            @include('tenant.settings.partials.tabs.api', ['apiTokens' => $apiTokens])

            {{-- Profile Identity Tab --}}
            @include('tenant.settings.partials.tabs.profile', ['user' => $user])

            {{-- Visual DNA (Branding) Tab --}}
            @include('tenant.settings.partials.tabs.branding', ['tenant' => $tenant])

            {{-- Billing & Treasury Tab --}}
            @include('tenant.settings.partials.tabs.billing', ['tokenBalance' => $tokenBalance, 'auditLogs' => $auditLogs])

            {{-- Security Hub Tab --}}
            @include('tenant.settings.partials.tabs.security', ['user' => $user])
        </div>
    </div>
</div>
@endsection