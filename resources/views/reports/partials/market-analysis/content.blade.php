{{-- Market Analysis - Content --}}
<div class="report-header">
    <div class="header-content">
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
    @if(isset($logoUrl) && $logoUrl)
        <img src="{{ $logoUrl }}" class="brand-logo" alt="Brand Logo">
    @endif
</div>

<div class="report-content">
    {!! $content !!}
</div>

<div class="footer">
    <div>ArchitGrid Market Archive</div>
    <div style="color: {{ $brandColor }};">Validated Strategy Node</div>
</div>
