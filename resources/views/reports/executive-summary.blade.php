{{--
    Executive Summary Report Template
    
    Executive intelligence brief with multiple variants.
    
    Required variables:
    - $content: AI-generated HTML content
    - $brandColor: Hex color for branding
    - $variant: Template variant (exec-standard, exec-minimal, exec-detailed)
    - $recipientName: Optional recipient name
    - $logoUrl: Optional brand logo URL
--}}

@extends('reports.layout')

@section('title', 'Executive Intelligence Brief')
@section('container_class', 'standard')

@section('styles')
    @include('reports.partials.executive-summary.styles')
@endsection

@section('content')
    @include('reports.partials.executive-summary.content')
@endsection