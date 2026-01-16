{{--
    Policy Create Page
    
    Create new security policy with ABAC logic builder.
    
    Features:
    - Visual logic node configuration
    - Effect and priority settings
    - JSON output preview
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
    @include('tenant.policies.partials.create-header')

    <div class="bg-card border border-border rounded-[40px] shadow-2xl relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
        
        @include('tenant.policies.partials.create-form')

        <!-- Technical Mark -->
        <div class="p-8 border-t border-border bg-muted/30 flex justify-between items-center opacity-30 mono text-[8px] font-black uppercase tracking-[0.4em]">
            <span>ArchitGrid Security Architect v1.0.4</span>
            <span>Policy Mode: Active ABAC</span>
        </div>
    </div>
</div>
@endsection
