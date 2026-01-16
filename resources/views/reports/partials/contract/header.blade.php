{{-- Contract Report - Header Section --}}
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
