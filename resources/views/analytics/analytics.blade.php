{{--
    Analytics Dashboard Page
    
    Grid telemetry with productivity metrics and consumption analysis.
    
    Required variables:
    - $successRate: Registry success percentage
    - $tokensConsumed: Total tokens used
    - $productivityIndex: Assets per identity ratio
    - $intelDensity: Assets per research node ratio
    - $intensityTrend: Array of daily consumption values
    - $labels: Array of day labels for chart
    
    Features:
    - Core metrics cards
    - Consumption intensity chart
    - Active nodes status
    - Intelligence flow analysis
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    {{-- Page Header --}}
    @include('analytics.partials.header')

    {{-- Core Grid Metrics --}}
    @include('analytics.partials.metrics')

    {{-- Telemetry Visualization --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        {{-- Consumption Chart --}}
        @include('analytics.partials.chart')

        {{-- Active Nodes --}}
        @include('analytics.partials.nodes')
    </div>

    {{-- Strategic Intelligence Feed --}}
    @include('analytics.partials.intelligence')
</div>
@endsection
