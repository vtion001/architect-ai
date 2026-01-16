{{-- Contract Report - Additional Styles --}}

/* Callout / Important Notices */
.callout, .important-notice { 
    background: #fffbeb; 
    border: 1px solid #fbbf24;
    border-left: 5px solid #f59e0b;
    padding: 18px 22px; 
    margin: 25px 0;
    font-size: 0.9rem;
}

.callout-critical {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-left: 5px solid #dc2626;
    padding: 18px 22px;
    margin: 25px 0;
    font-size: 0.9rem;
}

.callout-critical strong,
.important-notice strong {
    text-transform: uppercase;
    display: block;
    margin-bottom: 8px;
}

.callout-info {
    background: #f0f9ff;
    border: 1px solid #7dd3fc;
    border-left: 5px solid {{ $brandColor }};
    padding: 18px 22px;
    margin: 25px 0;
    font-size: 0.9rem;
}

/* Tables */
table { 
    width: 100%; 
    border: 1px solid #334155; 
    margin: 25px 0; 
    border-collapse: collapse;
    font-size: 0.9rem;
}

th { 
    background: #1e293b; 
    color: white;
    text-transform: uppercase; 
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    padding: 12px 15px;
    text-align: left;
}

td { 
    border: 1px solid #cbd5e1; 
    padding: 10px 15px; 
    text-align: left;
    vertical-align: top;
}

tr:nth-child(even) td {
    background: #f8fafc;
}

/* Payment Schedule Table */
.payment-table td:last-child,
.payment-table th:last-child {
    text-align: right;
    font-family: 'Roboto Mono', monospace;
}

.payment-table .total-row {
    background: #f1f5f9;
    font-weight: 700;
}

/* Milestone Timeline */
.milestone-block {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 4px solid {{ $brandColor }};
    padding: 20px;
    margin: 20px 0;
}

.milestone-block h4 {
    color: {{ $brandColor }};
    margin-top: 0;
}

/* Definition List for Terms */
dl {
    margin: 20px 0;
}

dt {
    font-weight: 700;
    color: #0f172a;
    margin-top: 12px;
}

dd {
    margin-left: 20px;
    margin-bottom: 10px;
    color: #475569;
}

/* Legal Emphasis */
.legal-emphasis {
    text-transform: uppercase;
    font-weight: 700;
    background: #fef3c7;
    padding: 2px 4px;
}

/* Signature Block */
.signature-section {
    margin-top: 80px;
    page-break-inside: avoid;
}

.signature-section h2 {
    border-color: #0f172a;
}

.signature-section h2::before {
    color: #0f172a;
}

.signature-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    margin-top: 40px;
}

.signature-block {
    padding-top: 15px;
}

.signature-block h4 {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #64748b;
    margin-bottom: 30px;
    margin-top: 0;
}

.signature-line {
    border-top: 1px solid #0f172a;
    padding-top: 8px;
    margin-bottom: 20px;
}

.signature-line-label {
    font-size: 0.8rem;
    color: #64748b;
    font-family: 'Inter', sans-serif;
}

.signature-line-value {
    font-size: 0.9rem;
    color: #0f172a;
    font-weight: 500;
    margin-top: 4px;
    min-height: 1.2em;
}

/* Footer Branding */
.contract-footer {
    margin-top: 60px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    text-align: center;
    font-family: 'Roboto Mono', monospace;
    font-size: 0.7rem;
    color: #94a3b8;
}

/* Print Optimizations */
@media print {
    .report-wrapper {
        font-size: 10pt;
    }
    
    .parties-section {
        break-inside: avoid;
    }
    
    .signature-section {
        break-inside: avoid;
    }
    
    h2, .article-header {
        break-after: avoid;
    }
    
    .milestone-block,
    .callout,
    .callout-critical,
    .callout-info {
        break-inside: avoid;
    }
}
