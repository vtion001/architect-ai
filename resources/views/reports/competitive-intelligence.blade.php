@extends('reports.layout')

@section('title', 'Competitive Intelligence')
@section('container_class', 'tech')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap');
    body { background: #0f172a; }
    .report-wrapper { font-family: 'JetBrains Mono', monospace; background: #f8fafc; border: 2px solid #334155; }
    .report-header { background: #334155; color: #22d3ee; padding: 30px 40px; border-bottom: 4px solid #22d3ee; }
    .report-header h1 { margin: 0; font-size: 1.8rem; font-weight: 700; text-transform: uppercase; }
    .report-meta { margin-top: 10px; font-size: 0.8rem; color: #94a3b8; }
    .report-content { padding: 40px; flex: 1; }
    h2 { background: #334155; color: white; padding: 10px 20px; display: inline-block; margin-top: 40px; font-size: 1.1rem; clip-path: polygon(0 0, 100% 0, 95% 100%, 0% 100%); }
    h3 { color: #0891b2; margin-top: 25px; border-left: 4px solid #22d3ee; padding-left: 15px; }
    p { margin: 15px 0; color: #334155; }
    code { background: #e2e8f0; padding: 2px 6px; border-radius: 3px; font-weight: 700; color: #0e7490; }
    .footer { padding: 20px 40px; background: #1e293b; color: #22d3ee; font-size: 0.75rem; display: flex; justify-content: space-between; margin-top: auto; }
@endsection

@section('content')
    <div class="report-header">
        <h1>Competitive Intelligence</h1>
        <div class="report-meta">CONFIDENTIAL // {{ date('Y-m-d') }}</div>
        @if($recipientName)
             <div style="margin-top: 5px; opacity: 0.8;">// RECIPIENT: {{ strtoupper($recipientName) }}</div>
        @endif
    </div>

    <div class="report-content">
        {!! $content !!}
    </div>

    <div class="footer">
        <span>ARCHITECT AI // SYSTEM GENERATED</span>
        <span>ID: {{ strtoupper(uniqid()) }}</span>
    </div>
@endsection
