{{-- Executive Summary - Content --}}
<div class="report-header">
    <div class="header-content">
        <h1>Executive <br>Intelligence brief</h1>
        <div class="report-meta">
            Session Protocol: {{ date('Y.m.d') }} / 0x{{ substr(md5((string)time()), 0, 8) }}
        </div>
        @if($recipientName)
            <div style="margin-top: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #94a3b8;">
                Authorized For: <span style="color: white;">{{ $recipientName }}</span>
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
    <div>ArchitGrid Registry v1.0.4</div>
    <div style="color: {{ $brandColor }};">Integrity Verified // AES-256</div>
</div>
