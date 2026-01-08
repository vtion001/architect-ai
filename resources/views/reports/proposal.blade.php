@extends('reports.layout')

@section('title', 'Business Proposal')
@section('container_class', 'proposal')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;900&display=swap');
    
    /* Proposal Theme */
    .report-wrapper { font-family: 'Inter', sans-serif; color: #1e293b; background: white; }
    
    .report-header { 
        background: #fff7ed; 
        color: #c2410c; 
        padding: 80px 60px; 
        border-bottom: 4px solid #f97316;
    }
    
    .report-header h1 { 
        font-family: 'Playfair Display', serif; 
        margin: 0; 
        font-size: 3.5rem; 
        font-weight: 900; 
        line-height: 1.1;
        color: #9a3412;
    }
    
    .report-meta { 
        margin-top: 20px; 
        font-size: 0.85rem; 
        font-weight: 600; 
        color: #ea580c;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    
    .report-content { padding: 60px; }
    
    h2 { 
        color: #9a3412; 
        font-family: 'Playfair Display', serif;
        font-size: 2rem; 
        margin-top: 50px; 
        margin-bottom: 20px;
        border-bottom: 1px solid #fed7aa;
        padding-bottom: 10px;
    }
    
    h3 { color: #c2410c; font-size: 1.25rem; margin-top: 30px; font-weight: 700; }
    
    .callout { 
        background: #fff7ed; 
        border-left: 4px solid #f97316; 
        padding: 25px; 
        margin: 30px 0; 
        color: #7c2d12;
    }
    
    table { width: 100%; border-collapse: collapse; margin: 30px 0; }
    th { background: #ffedd5; color: #9a3412; padding: 12px; text-align: left; font-weight: 700; border-bottom: 2px solid #f97316; }
    td { padding: 12px; border-bottom: 1px solid #fed7aa; color: #431407; }
    tr:nth-child(even) { background: #fffaf0; }

    /* Modern Pitch Variant */
    @if($variant === 'proposal-modern')
        .report-header { background: #111827; color: white; border-bottom: none; }
        .report-header h1 { color: white; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: -0.05em; }
        .report-meta { color: #fb923c; }
        h2 { color: #111827; font-family: 'Inter', sans-serif; font-weight: 900; letter-spacing: -0.03em; border-bottom: 4px solid #fb923c; }
        h3 { color: #374151; }
        th { background: #1f2937; color: white; border-bottom: none; }
        td { border-bottom: 1px solid #e5e7eb; }
        .callout { background: #1f2937; color: white; border-left: 4px solid #fb923c; }
    @endif
@endsection

@section('content')
    <div class="report-header">
        <h1>Project Proposal</h1>
        <div class="report-meta">
            Prepared For: {{ $recipientName }} <br>
            Date: {{ date('F d, Y') }}
        </div>
    </div>

    <div class="report-content">
        {!! $content !!}
    </div>

    <div class="footer" style="padding: 40px 60px; text-align: center; border-top: 1px solid #eee; font-size: 0.8rem; color: #999;">
        CONFIDENTIAL PROPOSAL &bull; VALID FOR 30 DAYS
    </div>
@endsection
