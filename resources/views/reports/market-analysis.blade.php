@extends('reports.layout')

@section('title', 'Market Intelligence Analysis')
@section('container_class', 'standard')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@800;900&display=swap');
    
    /* Global Intelligence Layout */
    .report-wrapper { font-family: 'Inter', sans-serif; color: #1e293b; background: white; }
    .report-header { 
        background: #064e3b; 
        color: #34d399; 
        padding: 80px 50px; 
        position: relative;
        border-bottom: 10px solid #059669;
    }
    .report-header h1 { 
        font-family: 'Montserrat', sans-serif; 
        margin: 0; 
        font-size: 3.5rem; 
        font-weight: 900; 
        text-transform: uppercase;
        letter-spacing: -0.05em;
        color: white;
        line-height: 0.85;
    }
    .report-meta { 
        margin-top: 25px; 
        font-size: 0.7rem; 
        font-weight: 900; 
        text-transform: uppercase;
        letter-spacing: 0.3em;
        background: rgba(52, 211, 153, 0.1);
        padding: 8px 16px;
        border-radius: 4px;
        display: inline-block;
    }
    
    .report-content { padding: 60px 50px; flex: 1; }
    
    h2 { 
        color: #064e3b; 
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        text-transform: uppercase;
        padding-bottom: 12px; 
        margin-top: 50px; 
        font-size: 1.4rem; 
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    h2::before {
        content: ''; width: 12px; height: 12px; background: #34d399; border-radius: 2px;
    }
    
    h3 { color: #065f46; margin-top: 30px; font-weight: 800; text-transform: uppercase; font-size: 1rem; }
    p { margin: 20px 0; color: #334155; line-height: 1.8; text-align: justify; }
    
    .callout { 
        background: #f0fdf4; 
        border-left: 6px solid #34d399; 
        padding: 30px; 
        margin: 40px 0; 
        border-radius: 0 20px 20px 0;
        font-weight: 500;
        color: #064e3b;
    }

    table { width: 100%; border-collapse: collapse; margin: 40px 0; font-size: 0.9rem; }
    th { background: #f8fafc; padding: 15px; text-align: left; font-weight: 900; text-transform: uppercase; color: #064e3b; border-bottom: 2px solid #34d399; }
    td { padding: 15px; border-bottom: 1px solid #e2e8f0; }

    .footer { 
        padding: 40px 50px; 
        background: #f8fafc;
        border-top: 1px solid #e2e8f0; 
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #64748b; 
        font-size: 0.7rem; 
        font-weight: 800;
        text-transform: uppercase;
    }

    /* Competitive Variant */
    @if($variant === 'market-competitive')
        .report-header { background: #0f172a; border-bottom-color: #3b82f6; color: #3b82f6; }
        h2::before { background: #3b82f6; }
        .callout { background: #eff6ff; border-left-color: #3b82f6; color: #1e3a8a; }
    @endif

    /* Segment Variant */
    @if($variant === 'market-segment')
        .report-header { background: white; color: #064e3b; padding: 60px 0; margin: 0 50px; border-bottom: 4px solid #064e3b; }
        .report-header h1 { color: #064e3b; }
    @endif
@endsection

@section('content')
    <div class="report-header">
        <h1>Market <br>Intelligence Registry</h1>
        <div class="report-meta">
            Domain Protocol: {{ date('Y.m.d') }} // ANALYTICS_NODE_0{{ rand(1,9) }}
        </div>
        @if($recipientName)
            <div style="margin-top: 25px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.6);">
                Registry Target: <span style="color: white;">{{ $recipientName }}</span>
            </div>
        @endif
    </div>

    <div class="report-content">
        {!! $content !!}
    </div>

    <div class="footer">
        <div>ArchitGrid Market Archive</div>
        <div style="color: #059669;">Validated Strategy Node</div>
    </div>
@endsection