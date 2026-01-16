{{--
    Legal Contract Report Template
    
    Modularized template with multiple contract variants.
    Styles and components extracted to partials for maintainability.
    
    Available variants:
    - contract-nda: Non-Disclosure Agreement (red accent)
    - contract-service: Service Agreement (brand color)
    - contract-employment: Employment Contract (teal accent)
    - contract-freelance: Freelance Agreement (purple accent)
    
    Required variables:
    - $variant: The contract style variant
    - $brandColor: Primary brand color
    - $content: Generated contract content (HTML)
    - $logoUrl: Optional brand logo URL
    - $contractNumber: Optional contract number
    - $effectiveDate: Optional effective date
    - $providerName, $providerTitle: Provider details
    - $recipientName, $recipientTitle: Client details
--}}

@extends('reports.layout')

@section('title', 'Legal Contract')
@section('container_class', 'contract')

@section('styles')
    {{-- Base Styles --}}
    @include('reports.partials.contract.styles-base')
    
    {{-- Component Styles (Tables, Signatures, etc.) --}}
    @include('reports.partials.contract.styles-components')
    
    {{-- Variant-Specific Styles --}}
    @include('reports.partials.contract.styles-variants')
@endsection

@section('content')
    {{-- Contract Header --}}
    @include('reports.partials.contract.header')

    <div class="report-content">
        {{-- Dynamic content from AI generation --}}
        {!! $content !!}

        {{-- Signature Section --}}
        @include('reports.partials.contract.signatures')
    </div>
@endsection
