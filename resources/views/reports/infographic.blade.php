@extends('reports.layout')

@section('title', 'Infographic Report')
@section('container_class', 'infographic-wrapper')

@section('styles')
    /* Reset & Fonts */
    .report-wrapper { font-family: 'Inter', system-ui, sans-serif; color: #1e293b; background: white; }
    
    /* Layout Grid */
    .grid-sections { display: grid; grid-template-columns: 30% 70%; gap: 30px; margin-bottom: 40px; }
    
    /* Header */
    .header { border-bottom: 2px solid #e2e8f0; padding: 40px 60px; margin-bottom: 30px; border-left: 10px solid #ec4899; }
    .header h1 { font-size: 2.5rem; color: #0f172a; text-transform: uppercase; letter-spacing: -1px; line-height: 1; margin: 0; }
    .header .subtitle { font-size: 0.9rem; color: #64748b; margin-top: 10px; }
    
    /* Sidebar (Left) */
    .sidebar-box { background: #0f172a; color: white; padding: 30px; border-radius: 4px; height: 100%; }
    .brand-icon { text-align: center; margin-bottom: 40px; }
    .brand-icon svg { width: 60px; height: 60px; stroke: white; opacity: 0.8; }
    .sidebar-section h3 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; margin-bottom: 20px; color: #94a3b8; }
    
    /* Main Content (Right) */
    .report-content { padding: 0 40px; flex: 1; }
    .report-content h2 { font-size: 1.8rem; color: #ec4899; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-top: 30px; }
    .report-content p { margin: 15px 0; font-size: 0.95rem; }

    /* Startup Variant Styles */
    @if($variant === 'infographic-startup')
        .header { border-left: 10px solid #7c3aed; background: #faf5ff; }
        .header h1 { color: #5b21b6; }
        .sidebar-box { background: #4c1d95; }
        .report-content h2 { color: #7c3aed; }
        .callout { background: #f5f3ff; border-left-color: #7c3aed; }
    @endif

    .footer { padding: 40px; border-top: 1px solid #f1f5f9; text-align: center; color: #94a3b8; font-size: 0.8rem; }
@endsection

@section('content')
    <div class="header">
        <h1>{!! $variant === 'infographic-startup' ? 'Startup Pivot<br>Analysis' : 'Intelligence<br>One-Pager' !!}</h1>
        <div class="subtitle">
            Secure Intelligence Registry | {{ date('F Y') }}
            @if($recipientName)
                <br>Prepared for: {{ $recipientName }} ({{ $recipientTitle ?? 'Lead' }})
            @endif
        </div>
    </div>

    <div class="grid-sections" style="padding: 0 60px;">
        <!-- Sidebar -->
        <div class="sidebar-box">
            <div class="brand-icon">
                <i data-lucide="sparkles" class="w-12 h-12 text-white/50"></i>
            </div>
            
            <div class="sidebar-section">
                <h3>Architecture</h3>
                <div style="font-size: 0.8rem; color: #cbd5e1; line-height: 1.6;">
                    Protocol: RAG-V3<br>
                    Grounding: Active<br>
                    Source: Hybrid Network
                </div>
            </div>
            
            <div style="margin-top: auto; padding-top: 40px;">
                <p style="font-[10px] text-slate-500 uppercase tracking-widest font-black">Confidential</p>
                <p style="font-[8px] text-slate-600 mono italic">Internal Sync Authorized</p>
            </div>
        </div>

        <!-- Right Content -->
        <div class="report-content">
            {!! $content !!}
        </div>
    </div>

    <div class="footer">
        ArchitGrid Protocol // Intelligence Visualization Division
    </div>
@endsection
