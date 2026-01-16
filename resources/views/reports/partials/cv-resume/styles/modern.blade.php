{{-- CV Resume - Modern Creative Variant Styles --}}
.report-wrapper {
    flex-direction: row;
    align-items: stretch;
    background: white;
}
.cv-sidebar {
    width: 32%;
    min-width: 260px;
    background: #f1f5f9;
    padding: 50px 30px;
    display: flex;
    flex-direction: column;
    gap: 40px;
    flex-shrink: 0;
    border-right: 1px solid #e2e8f0;
}
.cv-main {
    flex: 1;
    padding: 50px;
}
.cv-header { margin-bottom: 50px; }
.cv-header h1 {
    font-family: 'Inter', sans-serif;
    font-size: 3.5rem;
    font-weight: 900;
    line-height: 0.9;
    color: #0f172a;
    margin-bottom: 10px;
    letter-spacing: -0.04em;
}
.cv-header .role {
    font-size: 1.2rem;
    color: {{ $brandColor }};
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}
h2 {
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #94a3b8;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}
h2::after {
    content: '';
    flex: 1;
    height: 2px;
    background: #e2e8f0;
}

.sidebar-section h2 { color: {{ $brandColor }}; }
.sidebar-section h2::after { background: {{ $brandColor }}; opacity: 0.3; }

.skill-tag {
    display: inline-block;
    background: white;
    border: 1px solid #cbd5e1;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.75rem;
    margin: 0 6px 6px 0;
    font-weight: 700;
    color: #475569;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

h3 { font-size: 1.2rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
.job-meta { font-size: 0.85rem; color: {{ $brandColor }}; font-weight: 600; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.05em; }
