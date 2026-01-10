@extends('reports.layout')

@section('title', 'Business Proposal')
@section('container_class', 'proposal')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;900&display=swap');
    
    /* Proposal Theme */
    .report-wrapper { font-family: 'Inter', sans-serif; color: #1e293b; background: white; }
    
    .report-header { 
        background: {{ $brandColor }}10; 
        color: {{ $brandColor }}; 
        padding: 80px 60px; 
        border-bottom: 4px solid {{ $brandColor }};
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .brand-logo { height: 70px; width: auto; object-fit: contain; }
    
    .report-header h1 { 
        font-family: 'Playfair Display', serif; 
        margin: 0; 
        font-size: 3.5rem; 
        font-weight: 900; 
        line-height: 1.1;
        color: {{ $brandColor }};
        filter: brightness(0.8);
    }
    
    .report-meta { 
        margin-top: 20px; 
        font-size: 0.85rem; 
        font-weight: 600; 
        color: {{ $brandColor }};
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    
    .report-content { padding: 60px; }
    
    h2 { 
        color: {{ $brandColor }}; 
        font-family: 'Playfair Display', serif;
        font-size: 2rem; 
        margin-top: 50px; 
        margin-bottom: 20px;
        border-bottom: 1px solid {{ $brandColor }}40;
        padding-bottom: 10px;
        filter: brightness(0.8);
    }
    
    h3 { color: {{ $brandColor }}; font-size: 1.25rem; margin-top: 30px; font-weight: 700; }
    
    .callout { 
        background: {{ $brandColor }}08; 
        border-left: 4px solid {{ $brandColor }}; 
        padding: 25px; 
        margin: 30px 0; 
        color: #7c2d12;
    }
    
    table { width: 100%; border-collapse: collapse; margin: 30px 0; }
    th { background: {{ $brandColor }}20; color: {{ $brandColor }}; padding: 12px; text-align: left; font-weight: 700; border-bottom: 2px solid {{ $brandColor }}; filter: brightness(0.8); }
    td { padding: 12px; border-bottom: 1px solid {{ $brandColor }}20; color: #431407; }
    tr:nth-child(even) { background: {{ $brandColor }}05; }

    /* Modern Pitch Variant */
    @if($variant === 'proposal-modern')
        .report-header { background: #111827; color: white; border-bottom: none; }
        .report-header h1 { color: white; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: -0.05em; }
        .report-meta { color: {{ $brandColor }}; }
        h2 { color: #111827; font-family: 'Inter', sans-serif; font-weight: 900; letter-spacing: -0.03em; border-bottom: 4px solid {{ $brandColor }}; }
        h3 { color: #374151; }
        th { background: #1f2937; color: white; border-bottom: none; }
        td { border-bottom: 1px solid #e5e7eb; }
        .callout { background: #1f2937; color: white; border-left: 4px solid {{ $brandColor }}; }
        .brand-logo { background: white; padding: 5px; border-radius: 4px; }
    @endif
@endsection

@section('content')
    <div class="report-header">
        <div>
            <h1>Project Proposal</h1>
            <div class="report-meta">
                Prepared For: {{ $recipientName }} <br>
                Date: {{ date('F d, Y') }}
            </div>
        </div>
        @if(isset($logoUrl) && $logoUrl)
            <img src="{{ $logoUrl }}" class="brand-logo" alt="Brand Logo">
        @endif
    </div>

    <div class="report-content">
        {!! $content !!}
    </div>

    <div class="footer" style="padding: 40px 60px; text-align: center; border-top: 1px solid #eee; font-size: 0.8rem; color: #999;">
        CONFIDENTIAL PROPOSAL &bull; VALID FOR 30 DAYS
    </div>
@endsection
