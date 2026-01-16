{{-- Proposal - Content --}}
<div class="report-header">
    <div>
        <h1>Digital Infrastructure & Stewardship Proposal</h1>
        <div class="report-meta">
            @if(!empty($senderName))
                <strong>Strategic Partner (Architect):</strong> {{ $senderName }} @if(!empty($senderTitle)) ({{ $senderTitle }}) @endif <br>
            @endif
            <strong>Target Entity (Client):</strong> {{ $recipientName }} @if(!empty($recipientTitle)) ({{ $recipientTitle }}) @endif <br>
            <strong>Date of Issue:</strong> {{ date('F d, Y') }}
        </div>
    </div>
    @if(isset($logoUrl) && $logoUrl)
        <img src="{{ $logoUrl }}" class="brand-logo" alt="Brand Logo">
    @endif
</div>

<div class="report-content">
    {!! $content !!}

    @include('reports.partials.proposal.financials')
</div>

<div class="footer" style="padding: 40px 60px; text-align: center; border-top: 1px solid #eee; font-size: 0.8rem; color: #999;">
    PROPRIETARY ARCHITECTURE • OPERATIONAL BLUEPRINT • VALID FOR 30 DAYS
</div>