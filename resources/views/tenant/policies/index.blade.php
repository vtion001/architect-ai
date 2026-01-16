{{--
    Policies Index Page
    
    List and manage ABAC security policies.
    
    Required variables:
    - $policies: Collection of Policy models
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    @include('tenant.policies.partials.header')

    <!-- Security Health Bar -->
    @include('tenant.policies.partials.health-bar')

    <!-- Policy Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($policies as $policy)
            @include('tenant.policies.partials.policy-card')
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <i data-lucide="shield-off" class="w-16 h-16 mx-auto text-slate-300"></i>
                <p class="text-sm font-medium">Your grid is using default RBAC fallback logic. No ABAC protocols architected.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection