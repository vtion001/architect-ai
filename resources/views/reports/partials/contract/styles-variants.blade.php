{{-- Contract Report - Variant Styles --}}

/* NDA Variant */
@if($variant === 'contract-nda')
    .report-header { border-color: #dc2626; }
    .report-header h1 { color: #0f172a; }
    .report-header .contract-subtitle { color: #dc2626; }
    h2::before { color: #dc2626; }
    h2, .article-header { border-color: #dc2626; }
    .party-block h3 { color: #dc2626; border-color: #dc2626; }
    .recitals { border-color: #dc2626; background: linear-gradient(to right, #dc262608, transparent); }
    li::marker { color: #dc2626; }
    .milestone-block { border-left-color: #dc2626; }
@endif

/* Service Agreement Variant */
@if($variant === 'contract-service')
    .report-header .contract-subtitle::before { content: "INTERNATIONAL "; }
@endif

/* Employment Contract Variant */
@if($variant === 'contract-employment')
    .report-header { border-color: #0d9488; }
    .report-header .contract-subtitle { color: #0d9488; }
    h2::before { color: #0d9488; }
    h2, .article-header { border-color: #0d9488; }
    .party-block h3 { color: #0d9488; border-color: #0d9488; }
    .recitals { border-color: #0d9488; background: linear-gradient(to right, #0d948808, transparent); }
    li::marker { color: #0d9488; }
    .milestone-block { border-left-color: #0d9488; }
    .callout-info { border-left-color: #0d9488; }
@endif

/* Freelance Agreement Variant */
@if($variant === 'contract-freelance')
    .report-header { border-color: #7c3aed; }
    .report-header .contract-subtitle { color: #7c3aed; }
    h2::before { color: #7c3aed; }
    h2, .article-header { border-color: #7c3aed; }
    .party-block h3 { color: #7c3aed; border-color: #7c3aed; }
    .recitals { border-color: #7c3aed; background: linear-gradient(to right, #7c3aed08, transparent); }
    li::marker { color: #7c3aed; }
    .milestone-block { border-left-color: #7c3aed; }
    .callout-info { border-left-color: #7c3aed; }
@endif
