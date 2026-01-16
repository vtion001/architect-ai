{{--
    Market Analysis Report Template
    
    Market intelligence analysis with competitive insights.
    
    Required variables:
    - $content: AI-generated HTML content
    - $brandColor: Hex color for branding
    - $variant: Template variant (market-standard, market-competitive, market-segment)
    - $recipientName: Optional recipient name
    - $logoUrl: Optional brand logo URL
--}}

@extends('reports.layout')

@section('title', 'Market Intelligence Analysis')
@section('container_class', 'standard')

@section('styles')
    @include('reports.partials.market-analysis.styles')
@endsection

@section('content')
    @include('reports.partials.market-analysis.content')
@endsection