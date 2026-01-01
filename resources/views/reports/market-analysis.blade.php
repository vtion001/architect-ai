@extends('reports.layout')

@section('title', 'Market Analysis')
@section('container_class', 'modern')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    
    /* Default / Market Overview Style (market-overview) */
    .report-wrapper { font-family: 'Outfit', sans-serif; color: #064e3b; }
    .report-header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 50px 40px; position: relative; }
    .report-header h1 { margin: 0; font-size: 2.8rem; font-weight: 700; letter-spacing: -0.02em; }
    .report-meta { margin-top: 15px; background: rgba(255,255,255,0.2); display: inline-block; padding: 6px 12px; border-radius: 99px; font-size: 0.85rem; }
    .report-content { padding: 40px; flex: 1; }
    h2 { color: #059669; font-size: 1.8rem; margin-top: 45px; display: flex; align-items: center; gap: 12px; }
    h2::before { content: ""; width: 8px; height: 32px; background: #34d399; border-radius: 4px; }
    h3 { color: #065f46; margin-top: 25px; }
    p { margin: 16px 0; color: #374151; font-size: 1.05rem; }
    .highlight-box { background: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; border-radius: 0 12px 12px 0; margin: 25px 0; }
    .footer { padding: 30px; text-align: center; color: #6b7280; font-size: 0.9rem; background: #f9fafb; margin-top: auto; }

    /* Competitive Landscape Style (market-competitive) */
    @if($variant === 'market-competitive')
        .report-header { background: #064e3b; border-bottom: 8px solid #34d399; }
        h2 { border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        h2::before { display: none; }
        .report-content { background: #fafafa; }
    @endif

    /* Segment Analysis Style (market-segment) */
    @if($variant === 'market-segment')
        .report-header { background: white; color: #059669; border: 1px solid #e2e8f0; margin: 20px; border-radius: 12px; }
        .report-header h1 { font-size: 2.2rem; }
        .report-meta { background: #f0fdf4; color: #059669; }
        .report-content { padding-top: 0; }
    @endif
@endsection

@section('content')
    <div class="report-header">
        <h1>Market Analysis Report</h1>
        <div class="report-meta">Strategic Insights | {{ date('F j, Y') }}</div>
        @if($recipientName)
            <div style="margin-top: 15px; font-size: 0.9rem;">Prepared for: {{ $recipientName }}</div>
        @endif
    </div>

    <div class="report-content">
        {!! $content !!}
    </div>

    <div class="footer">
        Architect AI | Market Intelligence Division
    </div>
@endsection
