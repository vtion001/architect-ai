{{--
    Proposal Report Template
    
    Business proposal with investment and payment structure.
    
    Required variables:
    - $content: AI-generated HTML content
    - $brandColor: Hex color for branding
    - $variant: Template variant (proposal-classic, proposal-modern)
    - $recipientName, $recipientTitle: Recipient info
    - $senderName, $senderTitle: Optional sender info
    - $financials: Optional investment and milestones data
    - $logoUrl: Optional brand logo URL
--}}

@extends('reports.layout')

@section('title', 'Business Proposal')
@section('container_class', 'proposal')

@section('styles')
    @include('reports.partials.proposal.styles')
@endsection

@section('content')
    @include('reports.partials.proposal.content')
@endsection
