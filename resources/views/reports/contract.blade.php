@extends('reports.layout')

@section('title', 'Legal Contract')
@section('container_class', 'contract')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Roboto+Mono:wght@400;500&display=swap');
    
    /* Legal Theme */
    .report-wrapper { font-family: 'Merriweather', serif; color: #0f172a; background: white; line-height: 1.9; }
    
    .report-header { 
        text-align: center;
        padding: 60px 60px 40px; 
        border-bottom: 2px solid {{ $brandColor }};
        margin-bottom: 40px;
    }
    
    .brand-logo { height: 60px; width: auto; object-fit: contain; margin-bottom: 20px; }
    
    .report-header h1 { 
        font-size: 2.5rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 0.05em;
        margin-bottom: 10px;
        color: #0f172a;
    }
    
    .report-meta { 
        font-family: 'Roboto Mono', monospace;
        font-size: 0.8rem;
        color: #64748b;
    }
    
    .report-content { padding: 0 60px 60px; font-size: 1rem; }
    
    h2 { 
        font-size: 1.2rem; 
        text-transform: uppercase; 
        border-bottom: 1px solid #e2e8f0; 
        padding-bottom: 5px; 
        margin-top: 40px; 
        font-weight: 700;
        color: #0f172a;
    }
    
    h3 { font-size: 1.1rem; font-style: italic; margin-top: 30px; color: {{ $brandColor }}; filter: brightness(0.8); }
    
    p { margin-bottom: 1.5rem; text-align: justify; }
    
    .callout { 
        background: #f8fafc; 
        border: 1px solid #cbd5e1; 
        border-left: 4px solid {{ $brandColor }};
        padding: 20px; 
        margin: 30px 0; 
        font-family: 'Roboto Mono', monospace; 
        font-size: 0.9rem;
    }
    
    table { width: 100%; border: 1px solid #0f172a; margin: 30px 0; border-collapse: collapse; }
    th, td { border: 1px solid #0f172a; padding: 10px; text-align: left; }
    th { background: #f1f5f9; text-transform: uppercase; font-size: 0.8rem; }

    .signature-block {
        margin-top: 80px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        page-break-inside: avoid;
    }
    
    .signature-line {
        border-top: 1px solid #0f172a;
        padding-top: 10px;
        font-size: 0.9rem;
        font-weight: 700;
    }

    /* NDA Variant */
    @if($variant === 'contract-nda')
        .report-header h1 { color: #dc2626; border-color: #dc2626; }
        .report-wrapper { border-left: 10px solid #fef2f2; }
    @endif
@endsection

@section('content')
    <div class="report-header">
        @if(isset($logoUrl) && $logoUrl)
            <img src="{{ $logoUrl }}" class="brand-logo" alt="Brand Logo">
        @endif
        <h1>{{ $variant === 'contract-nda' ? 'Non-Disclosure Agreement' : 'Service Agreement' }}</h1>
        <div class="report-meta">
            Reference: #{{ strtoupper(substr(md5(time()), 0, 8)) }} <br>
            Effective Date: {{ date('F d, Y') }}
        </div>
    </div>

    <div class="report-content">
        <p><strong>BETWEEN:</strong> {{ $recipientName }} ("Client") AND ArchitectAI ("Provider").</p>
        
        {!! $content !!}

        <div class="signature-block">
            <div>
                <div class="signature-line">Signed by Client</div>
                <div style="margin-top: 5px; font-size: 0.8rem; color: #64748b;">Date: __________________</div>
            </div>
            <div>
                <div class="signature-line">Signed by Provider</div>
                <div style="margin-top: 5px; font-size: 0.8rem; color: #64748b;">Date: {{ date('Y-m-d') }}</div>
            </div>
        </div>
    </div>
@endsection
