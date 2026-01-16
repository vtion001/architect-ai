{{--
    Dashboard - Command Center
    
    Main dashboard with metrics, quick actions, and activity stream.
    Modularized - uses @include for partials.
    
    Required variables:
    - $contentCount: Number of content pieces generated
    - $researchCount: Number of research sessions
    - $tokenBalance: Available tokens
    - $kbCount: Knowledge base document count
    - $recentActivities: Array of recent activity items
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto animate-in fade-in duration-700">
    
    {{-- Welcome Header --}}
    @include('dashboard.partials.header')

    {{-- ROI & Intelligence Matrix --}}
    @include('dashboard.partials.metrics-grid', [
        'contentCount' => $contentCount,
        'researchCount' => $researchCount,
        'tokenBalance' => $tokenBalance
    ])

    {{-- Quick Start: Brand DNA & Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        {{-- Main Action Area --}}
        @include('dashboard.partials.quick-actions')

        {{-- Sidebar: Knowledge Status --}}
        @include('dashboard.partials.knowledge-sidebar', ['kbCount' => $kbCount])
    </div>

    {{-- Recent Activity Table --}}
    @include('dashboard.partials.activity-stream', ['recentActivities' => $recentActivities])
</div>
@endsection