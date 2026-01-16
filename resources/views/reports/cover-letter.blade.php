{{--
    Cover Letter Report Template
    
    Professional cover letter with standard and creative variants.
    
    Required variables:
    - $content: AI-generated letter content
    - $brandColor: Hex color for branding
    - $variant: Template variant (cl-standard, cl-creative)
    - $senderName, $senderTitle: Sender info
    - $recipientName, $recipientTitle: Optional recipient info
    - $companyAddress: Optional company address
    - $contactInfo: Array with email, phone, location, website
--}}

@extends('reports.layout')

@section('title', 'Cover Letter')
@section('container_class', 'cover-letter')

@section('styles')
    @include('reports.partials.cover-letter.styles')
@endsection

@section('content')
    @if($variant === 'cl-creative')
        @include('reports.partials.cover-letter.header-creative')
    @else
        @include('reports.partials.cover-letter.header-standard')
    @endif

    @include('reports.partials.cover-letter.content')
@endsection
