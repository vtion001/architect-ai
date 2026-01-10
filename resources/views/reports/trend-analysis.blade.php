@extends('reports.layout')

@section('title', 'Trend Analysis')
@section('container_class', 'minimal')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');
    body { background: white; padding: 0; }
    .report-wrapper { width: 210mm; min-height: 297mm; box-shadow: none; border-left: 1px solid #eee; border-right: 1px solid #eee; }
    .report-header { padding: 80px 60px; background: #000; color: white; text-align: left; display: flex; flex-direction: column; align-items: flex-start; }
    .brand-logo { height: 60px; width: auto; object-fit: contain; margin-bottom: 30px; background: white; padding: 5px; }
    .report-header h1 { font-size: 4rem; margin: 0; font-weight: 700; line-height: 1; letter-spacing: -2px; color: white; }
    .report-meta { margin-top: 20px; font-size: 1rem; color: #999; text-transform: uppercase; letter-spacing: 2px; }
    .report-content { padding: 60px; flex: 1; }
    h2 { font-size: 2.5rem; font-weight: 700; margin-top: 60px; color: {{ $brandColor }}; letter-spacing: -1px; }
    h3 { font-size: 1.5rem; color: #444; margin-top: 30px; }
    p { font-size: 1.15rem; color: #333; margin: 20px 0; }
    .footer { padding: 60px; background: #f5f5f5; text-align: center; font-weight: 600; text-transform: uppercase; letter-spacing: 3px; margin-top: auto; color: {{ $brandColor }}; }
@endsection

@section('content')
    <div class="report-header">
        @if(isset($logoUrl) && $logoUrl)
            <img src="{{ $logoUrl }}" class="brand-logo" alt="Brand Logo">
        @endif
        <h1>Trend Analysis</h1>
        <div class="report-meta">Future Outlook {{ date('Y') }}</div>
        @if($recipientName)
            <div style="margin-top: 20px; font-size: 1rem; color: #999; text-transform: none; letter-spacing: 0;">Prepared for {{ $recipientName }}</div>
        @endif
    </div>

    <div class="report-content">
        {!! $content !!}
    </div>

    <div class="footer">
        ArchitGrid Research
    </div>
@endsection
