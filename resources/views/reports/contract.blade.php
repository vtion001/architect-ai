@extends('reports.layout')

@section('title', 'Legal Contract')
@section('container_class', 'contract')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Roboto+Mono:wght@400;500&family=Inter:wght@400;500;600;700&display=swap');
    
    /* Professional Legal Contract Theme */
    .report-wrapper { 
        font-family: 'Merriweather', Georgia, serif; 
        color: #0f172a; 
        background: white; 
        line-height: 1.8;
        font-size: 11pt;
    }
    
    /* Document Header */
    .report-header { 
        text-align: center;
        padding: 50px 60px 30px; 
        border-bottom: 3px double {{ $brandColor }};
        margin-bottom: 30px;
    }
    
    .brand-logo { 
        height: 50px; 
        width: auto; 
        object-fit: contain; 
        margin-bottom: 15px; 
    }
    
    .report-header h1 { 
        font-size: 1.8rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 0.08em;
        margin-bottom: 5px;
        color: #0f172a;
    }
    
    .report-header .contract-subtitle {
        font-size: 1rem;
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: {{ $brandColor }};
        margin-bottom: 20px;
    }
    
    .report-meta { 
        font-family: 'Roboto Mono', monospace;
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 15px;
    }
    
    .report-meta .contract-number {
        font-weight: 600;
        color: #0f172a;
    }
    
    /* Main Content */
    .report-content { 
        padding: 0 60px 60px; 
        font-size: 11pt;
    }
    
    /* Parties Section */
    .parties-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin: 30px 0 40px;
        padding: 25px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
    }
    
    .party-block h3 {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: {{ $brandColor }};
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid {{ $brandColor }};
    }
    
    .party-block .party-field {
        display: flex;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    
    .party-block .party-label {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        color: #475569;
        min-width: 100px;
    }
    
    .party-block .party-value {
        color: #0f172a;
        border-bottom: 1px dotted #cbd5e1;
        flex: 1;
        min-height: 1.2em;
    }
    
    /* Recitals / Whereas */
    .recitals {
        margin: 30px 0;
        padding: 20px 25px;
        background: linear-gradient(to right, {{ $brandColor }}08, transparent);
        border-left: 4px solid {{ $brandColor }};
        font-style: italic;
    }
    
    .recitals p {
        margin-bottom: 1rem;
    }
    
    .recitals p:last-child {
        margin-bottom: 0;
    }
    
    .therefore-clause {
        font-weight: 700;
        font-style: normal;
        margin-top: 1.5rem !important;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.02em;
    }
    
    /* Article Headers */
    h2, .article-header { 
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem; 
        text-transform: uppercase; 
        letter-spacing: 0.05em;
        border-bottom: 2px solid {{ $brandColor }}; 
        padding-bottom: 8px; 
        margin-top: 45px;
        margin-bottom: 20px;
        font-weight: 700;
        color: #0f172a;
    }
    
    h2::before, .article-header::before {
        content: "◆ ";
        color: {{ $brandColor }};
    }
    
    /* Section Headers */
    h3, .section-header { 
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem; 
        font-weight: 600;
        margin-top: 25px;
        margin-bottom: 12px;
        color: #1e293b;
    }
    
    /* Subsection Headers */
    h4, .subsection-header {
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 18px;
        margin-bottom: 8px;
        color: #334155;
    }
    
    /* Paragraphs */
    p { 
        margin-bottom: 1rem; 
        text-align: justify; 
        text-justify: inter-word;
    }
    
    /* Lists */
    ul, ol {
        margin: 15px 0;
        padding-left: 25px;
    }
    
    li {
        margin-bottom: 8px;
        text-align: justify;
    }
    
    li::marker {
        color: {{ $brandColor }};
        font-weight: 700;
    }
    
    /* Callout / Important Notices */
    .callout, .important-notice { 
        background: #fffbeb; 
        border: 1px solid #fbbf24;
        border-left: 5px solid #f59e0b;
        padding: 18px 22px; 
        margin: 25px 0;
        font-size: 0.9rem;
    }
    
    .callout-critical {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-left: 5px solid #dc2626;
        padding: 18px 22px;
        margin: 25px 0;
        font-size: 0.9rem;
    }
    
    .callout-critical strong,
    .important-notice strong {
        text-transform: uppercase;
        display: block;
        margin-bottom: 8px;
    }
    
    .callout-info {
        background: #f0f9ff;
        border: 1px solid #7dd3fc;
        border-left: 5px solid {{ $brandColor }};
        padding: 18px 22px;
        margin: 25px 0;
        font-size: 0.9rem;
    }
    
    /* Tables */
    table { 
        width: 100%; 
        border: 1px solid #334155; 
        margin: 25px 0; 
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    
    th { 
        background: #1e293b; 
        color: white;
        text-transform: uppercase; 
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        padding: 12px 15px;
        text-align: left;
    }
    
    td { 
        border: 1px solid #cbd5e1; 
        padding: 10px 15px; 
        text-align: left;
        vertical-align: top;
    }
    
    tr:nth-child(even) td {
        background: #f8fafc;
    }
    
    /* Payment Schedule Table */
    .payment-table td:last-child,
    .payment-table th:last-child {
        text-align: right;
        font-family: 'Roboto Mono', monospace;
    }
    
    .payment-table .total-row {
        background: #f1f5f9;
        font-weight: 700;
    }
    
    /* Milestone Timeline */
    .milestone-block {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 4px solid {{ $brandColor }};
        padding: 20px;
        margin: 20px 0;
    }
    
    .milestone-block h4 {
        color: {{ $brandColor }};
        margin-top: 0;
    }
    
    /* Definition List for Terms */
    dl {
        margin: 20px 0;
    }
    
    dt {
        font-weight: 700;
        color: #0f172a;
        margin-top: 12px;
    }
    
    dd {
        margin-left: 20px;
        margin-bottom: 10px;
        color: #475569;
    }
    
    /* Legal Emphasis */
    .legal-emphasis {
        text-transform: uppercase;
        font-weight: 700;
        background: #fef3c7;
        padding: 2px 4px;
    }
    
    /* Signature Block */
    .signature-section {
        margin-top: 80px;
        page-break-inside: avoid;
    }
    
    .signature-section h2 {
        border-color: #0f172a;
    }
    
    .signature-section h2::before {
        color: #0f172a;
    }
    
    .signature-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        margin-top: 40px;
    }
    
    .signature-block {
        padding-top: 15px;
    }
    
    .signature-block h4 {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
        margin-bottom: 30px;
        margin-top: 0;
    }
    
    .signature-line {
        border-top: 1px solid #0f172a;
        padding-top: 8px;
        margin-bottom: 20px;
    }
    
    .signature-line-label {
        font-size: 0.8rem;
        color: #64748b;
        font-family: 'Inter', sans-serif;
    }
    
    .signature-line-value {
        font-size: 0.9rem;
        color: #0f172a;
        font-weight: 500;
        margin-top: 4px;
        min-height: 1.2em;
    }
    
    /* Footer Branding */
    .contract-footer {
        margin-top: 60px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
        font-family: 'Roboto Mono', monospace;
        font-size: 0.7rem;
        color: #94a3b8;
    }
    
    /* Print Optimizations */
    @media print {
        .report-wrapper {
            font-size: 10pt;
        }
        
        .parties-section {
            break-inside: avoid;
        }
        
        .signature-section {
            break-inside: avoid;
        }
        
        h2, .article-header {
            break-after: avoid;
        }
        
        .milestone-block,
        .callout,
        .callout-critical,
        .callout-info {
            break-inside: avoid;
        }
    }

    /* NDA Variant */
    @if($variant === 'contract-nda')
        .report-header { border-color: #dc2626; }
        .report-header h1 { color: #0f172a; }
        .report-header .contract-subtitle { color: #dc2626; }
        h2::before { color: #dc2626; }
        h2, .article-header { border-color: #dc2626; }
        .party-block h3 { color: #dc2626; border-color: #dc2626; }
        .recitals { border-color: #dc2626; background: linear-gradient(to right, #dc262608, transparent); }
        li::marker { color: #dc2626; }
        .milestone-block { border-left-color: #dc2626; }
    @endif
    
    /* Service Agreement Variant */
    @if($variant === 'contract-service')
        .report-header .contract-subtitle::before { content: "INTERNATIONAL "; }
    @endif
    
    /* Employment Contract Variant */
    @if($variant === 'contract-employment')
        .report-header { border-color: #0d9488; }
        .report-header .contract-subtitle { color: #0d9488; }
        h2::before { color: #0d9488; }
        h2, .article-header { border-color: #0d9488; }
        .party-block h3 { color: #0d9488; border-color: #0d9488; }
        .recitals { border-color: #0d9488; background: linear-gradient(to right, #0d948808, transparent); }
        li::marker { color: #0d9488; }
        .milestone-block { border-left-color: #0d9488; }
        .callout-info { border-left-color: #0d9488; }
    @endif
    
    /* Freelance Agreement Variant */
    @if($variant === 'contract-freelance')
        .report-header { border-color: #7c3aed; }
        .report-header .contract-subtitle { color: #7c3aed; }
        h2::before { color: #7c3aed; }
        h2, .article-header { border-color: #7c3aed; }
        .party-block h3 { color: #7c3aed; border-color: #7c3aed; }
        .recitals { border-color: #7c3aed; background: linear-gradient(to right, #7c3aed08, transparent); }
        li::marker { color: #7c3aed; }
        .milestone-block { border-left-color: #7c3aed; }
        .callout-info { border-left-color: #7c3aed; }
    @endif
@endsection

@section('content')
    <div class="report-header">
        @if(isset($logoUrl) && $logoUrl)
            <img src="{{ $logoUrl }}" class="brand-logo" alt="Brand Logo">
        @endif
        
        @if($variant === 'contract-nda')
            <h1>Non-Disclosure Agreement</h1>
            <div class="contract-subtitle">Confidentiality Agreement</div>
        @elseif($variant === 'contract-service')
            <h1>Service Agreement</h1>
            <div class="contract-subtitle">International Services Agreement</div>
        @elseif($variant === 'contract-employment')
            <h1>Employment Contract</h1>
            <div class="contract-subtitle">Employment Agreement</div>
        @elseif($variant === 'contract-freelance')
            <h1>Freelance Agreement</h1>
            <div class="contract-subtitle">Independent Contractor Agreement</div>
        @else
            <h1>Service Agreement</h1>
            <div class="contract-subtitle">Professional Services Contract</div>
        @endif
        
        <div class="report-meta">
            <strong class="contract-number">Contract No: {{ isset($contractNumber) ? $contractNumber : strtoupper(substr(md5(time()), 0, 8)) }}</strong><br>
            Effective Date: {{ isset($effectiveDate) ? $effectiveDate : date('F d, Y') }}
        </div>
    </div>

    <div class="report-content">
        {{-- Dynamic content from AI generation --}}
        {!! $content !!}

        {{-- Signature Section --}}
        <div class="signature-section">
            <h2>Signatures</h2>
            <p>IN WITNESS WHEREOF, the parties have executed this Agreement as of the Contract Date first above written.</p>
            
            <div class="signature-grid">
                <div class="signature-block">
                    <h4>Service Provider</h4>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Signature</div>
                        <div class="signature-line-value"></div>
                    </div>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Printed Name</div>
                        <div class="signature-line-value">{{ isset($providerName) ? $providerName : '___________________________' }}</div>
                    </div>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Title</div>
                        <div class="signature-line-value">{{ isset($providerTitle) ? $providerTitle : '___________________________' }}</div>
                    </div>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Date</div>
                        <div class="signature-line-value">___________________________</div>
                    </div>
                </div>
                
                <div class="signature-block">
                    <h4>Client</h4>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Signature</div>
                        <div class="signature-line-value"></div>
                    </div>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Printed Name</div>
                        <div class="signature-line-value">{{ $recipientName ?? '___________________________' }}</div>
                    </div>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Title</div>
                        <div class="signature-line-value">{{ isset($recipientTitle) ? $recipientTitle : '___________________________' }}</div>
                    </div>
                    
                    <div class="signature-line">
                        <div class="signature-line-label">Date</div>
                        <div class="signature-line-value">___________________________</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="contract-footer">
            Generated by ArchitGrid Intelligence Node v1.0.4
        </div>
    </div>
@endsection
