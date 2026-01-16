{{-- Proposal - Content --}}
<div class="report-header">
    <div>
        <h1>Project Proposal</h1>
        <div class="report-meta">
            @if(!empty($senderName))
                From: {{ $senderName }} @if(!empty($senderTitle)) ({{ $senderTitle }}) @endif <br>
            @endif
            Prepared For: {{ $recipientName }} @if(!empty($recipientTitle)) ({{ $recipientTitle }}) @endif <br>
            Date: {{ date('F d, Y') }}
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
    CONFIDENTIAL PROPOSAL &bull; VALID FOR 30 DAYS
</div>
