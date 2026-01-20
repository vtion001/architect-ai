{{--
    Content Creator - Main Layout
    
    Modularized & Refactored.
    All sections extracted to partials in /partials directory.
    Alpine.js logic extracted to /resources/js/components/content-creator.js
--}}
@extends('layouts.app')

@section('content')
<script>
    // Pass PHP data to JavaScript
    window.__contentCreatorBrands = @json($brands ?? []);
</script>

<div class="p-8 max-w-7xl mx-auto" x-data="contentCreator">

    {{-- Header Info & Generator Toggles --}}
    @include('content-creator.partials.header-info')

    {{-- Stats Cards --}}
    @include('content-creator.partials.stats-cards')

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Content Generator Panel --}}
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2 overflow-hidden">
            <div class="bg-muted/50 border-b border-border p-3 text-center">
                <span class="text-sm font-semibold tracking-wide uppercase" 
                      x-text="generator.charAt(0).toUpperCase() + generator.slice(1) + ' Architect'"></span>
            </div>
            
            <div class="p-8 space-y-8">
                {{-- Video Generator Interface --}}
                @include('content-creator.partials.generators.video-generator')

                {{-- Post Generator Interface --}}
                @include('content-creator.partials.generators.post-generator')

                {{-- Blog Generator Interface --}}
                @include('content-creator.partials.generators.blog-generator')

                {{-- Framework Calendar Interface --}}
                @include('content-creator.partials.generators.framework-generator')
            </div>
        </div>

        {{-- Sidebar: Context Aware Content --}}
        <div class="space-y-6">
            {{-- Calendar Result View (Active when framework generated) --}}
            @include('content-creator.partials.sidebar.calendar-results')

            {{-- Normal Mode: Recent Content Feed --}}
            @include('content-creator.partials.sidebar.recent-activity')

            {{-- Video Mode: How It Works & Tokens --}}
            @include('content-creator.partials.sidebar.video-info')
        </div>
    </div>

    {{-- Bulk Schedule Modal --}}
    @include('content-creator.partials.modals.bulk-schedule-modal')

    {{-- Success Modal --}}
    @include('content-creator.partials.success-modal')

</div>

@push('scripts')
    {{-- Load the JS component via Vite --}}
    @vite('resources/js/components/content-creator.js')
@endpush
@endsection