{{--
    Research Report View
    
    Displays research results with intelligence metrics and verified sources.
    Supports loading, failed, and complete states.
    
    Required variables:
    - $research: Research model with status, title, query, result, sources_count, options
    
    Features:
    - Animated loading state with auto-refresh
    - Error state with retry link
    - Intelligence metrics sidebar
    - Mermaid diagram rendering
    - Automatic source extraction
--}}

@extends('layouts.app')

@section('content')
    @if($research->status === 'researching')
        {{-- Loading State --}}
        @include('research-engine.partials.report.loading')
        
    @elseif($research->status === 'failed')
        {{-- Failed State --}}
        @include('research-engine.partials.report.failed')
        
    @else
        {{-- Completed Report --}}
        <div class="p-10 max-w-[1400px] mx-auto animate-in fade-in duration-700">
            {{-- Protocol Header --}}
            @include('research-engine.partials.report.header')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                {{-- Sidebar: Metrics --}}
                @include('research-engine.partials.report.sidebar')

                {{-- Main Intelligence Display --}}
                @include('research-engine.partials.report.content')
            </div>
        </div>
    @endif

    {{-- Scripts --}}
    @include('research-engine.partials.report.scripts')
    
    {{-- Styles --}}
    @include('research-engine.partials.report.styles')
@endsection