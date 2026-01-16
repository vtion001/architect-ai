{{--
    Research Engine Index Page
    
    Deep research protocol interface with web grounding capabilities.
    
    Required variables:
    - $stats: Array with total_reports, active_research, sources_analyzed, success_rate
    - $recentResearches: Collection of recent research records
    
    Features:
    - Research initiation form
    - Protocol history with status tracking
    - Stats dashboard
    - Success modal on completion
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{ 
    researchTitle: '',
    researchQuery: '',
    isResearching: false,
    showSuccessModal: false,
    createdResearchId: null,

    startResearch() {
        if (!this.researchTitle || !this.researchQuery) {
            alert('Please fill in both title and query.');
            return;
        }
        this.isResearching = true;
        fetch('{{ route('research-engine.start') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: this.researchTitle,
                query: this.researchQuery
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.createdResearchId = data.research.id;
                this.showSuccessModal = true;
                this.isResearching = false;
            } else {
                alert('Research failed: ' + (data.message || 'Unknown error'));
                this.isResearching = false;
            }
        })
        .catch(err => {
            console.error('Research Engine Error:', err);
            this.isResearching = false;
        });
    }
}">
    {{-- Page Header --}}
    @include('research-engine.partials.engine.header')

    {{-- Stats Cards --}}
    @include('research-engine.partials.engine.stats')

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
        {{-- New Research Form --}}
        @include('research-engine.partials.engine.form')

        {{-- Recent Research History --}}
        @include('research-engine.partials.engine.history')
    </div>

    {{-- Success Modal --}}
    @include('research-engine.partials.engine.success-modal')
</div>
@endsection
