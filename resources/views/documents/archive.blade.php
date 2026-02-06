{{--
    Documents Index Page
    
    Archive of generated documents and reports.
    
    Required variables:
    - $documents: Collection of Document models
    - $stats: Array with total_assets, report_count, storage_used
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    @include('documents.partials.index.header')

    <!-- Archive Telemetry -->
    @include('documents.partials.index.stats')

    <!-- Documents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($documents as $doc)
            @include('documents.partials.index.document-card')
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <i data-lucide="archive" class="w-16 h-16 mx-auto text-slate-300"></i>
                <p class="text-sm font-medium">Archive protocol initialized but no assets found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
