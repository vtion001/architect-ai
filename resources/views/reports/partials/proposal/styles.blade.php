{{-- Proposal - Styles --}}
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap');

/* Proposal Theme - Architectural Standard */
.report-wrapper { font-family: 'Inter', sans-serif; color: #1e293b; background: white; }

.report-header { 
    background: {{ $brandColor }}08; 
    color: {{ $brandColor }}; 
    padding: 80px 60px; 
    border-bottom: 2px solid {{ $brandColor }};
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.brand-logo { height: 60px; width: auto; object-fit: contain; mix-blend-mode: multiply; }

.report-header h1 { 
    font-family: 'Inter', sans-serif; 
    margin: 0 0 20px 0; 
    font-size: 2.5rem; 
    font-weight: 800; 
    line-height: 1.1;
    color: {{ $brandColor }};
    letter-spacing: -0.03em;
    text-transform: uppercase;
}

.report-meta { 
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem; 
    font-weight: 500; 
    color: #64748b;
    line-height: 1.6;
}
.report-meta strong { color: {{ $brandColor }}; }

.report-content { padding: 60px; }

h2 { 
    color: #0f172a; 
    font-family: 'Inter', sans-serif;
    font-size: 1.5rem; 
    margin-top: 50px; 
    margin-bottom: 20px;
    border-left: 4px solid {{ $brandColor }};
    padding-left: 15px;
    font-weight: 800;
    letter-spacing: -0.02em;
    text-transform: uppercase;
}

h3 { color: #334155; font-size: 1.1rem; margin-top: 30px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }

/* System Alert Callout */
.callout { 
    background: #f8fafc; 
    border: 1px solid #e2e8f0;
    border-left: 4px solid {{ $brandColor }}; 
    padding: 20px 25px; 
    margin: 30px 0; 
    color: #334155;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

table { width: 100%; border-collapse: collapse; margin: 30px 0; font-family: 'Inter', sans-serif; }
th { background: #f1f5f9; color: #475569; padding: 12px 16px; text-align: left; font-weight: 700; border-bottom: 2px solid #e2e8f0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 0.95rem; }
tr:last-child td { border-bottom: none; }

/* Modern Pitch Variant Override */
@if($variant === 'proposal-modern')
    .report-header { background: #0f172a; color: white; border-bottom: none; }
    .report-header h1 { color: white; }
    .report-meta { color: #94a3b8; }
    .report-meta strong { color: {{ $brandColor }}; }
    h2 { border-left: none; border-bottom: 2px solid {{ $brandColor }}; padding-left: 0; padding-bottom: 10px; color: #0f172a; }
    th { background: #1e293b; color: white; border: none; }
    .callout { background: #1e293b; color: #e2e8f0; border: none; border-left: 4px solid {{ $brandColor }}; }
    .brand-logo { background: white; padding: 5px; border-radius: 4px; }
@endif