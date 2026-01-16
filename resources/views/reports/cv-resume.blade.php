{{--
    CV / Resume Report Template
    
    Modularized template with multiple variant styles.
    Styles and layouts are extracted to partials for maintainability.
    
    Available variants:
    - cv-classic: Classic Professional (centered)
    - cv-modern: Modern Creative (sidebar layout)
    - cv-technical: Technical Expert (top-down, monospace)
    - cv-international: International Standard (Healthcare/MLS)
    
    Required variables:
    - $variant: The CV style variant
    - $brandColor: Primary brand color
    - $recipientName: Candidate name
    - $recipientTitle: Professional role/title
    - $contactInfo: Array with email, phone, location, website
    - $personalInfo: Array with age, dob, gender, etc.
    - $content: Generated CV content (HTML)
    - $profilePhotoUrl / $profile_photo_url: Optional photo URL
    - $logoUrl: Optional logo URL
--}}

@extends('reports.layout')

@section('title', 'CV / Resume')
@section('container_class', 'cv-resume')

@section('styles')
    {{-- Base Styles --}}
    @include('reports.partials.cv-resume.styles.base')

    {{-- Variant-specific styles --}}
    @if($variant === 'cv-classic')
        @include('reports.partials.cv-resume.styles.classic')
    @elseif($variant === 'cv-modern')
        @include('reports.partials.cv-resume.styles.modern')
    @elseif($variant === 'cv-international')
        @include('reports.partials.cv-resume.styles.international')
    @else
        {{-- Default to technical --}}
        @include('reports.partials.cv-resume.styles.technical')
    @endif
@endsection

@section('content')
    {{-- Variant-specific layouts --}}
    @if($variant === 'cv-modern')
        @include('reports.partials.cv-resume.layouts.modern')
    @elseif($variant === 'cv-classic')
        @include('reports.partials.cv-resume.layouts.classic')
    @elseif($variant === 'cv-international')
        @include('reports.partials.cv-resume.layouts.international')
    @else
        {{-- Default to technical layout --}}
        @include('reports.partials.cv-resume.layouts.technical')
    @endif
@endsection
