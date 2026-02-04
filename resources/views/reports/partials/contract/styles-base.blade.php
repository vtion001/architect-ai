{{-- Contract Report - Base Styles --}}
@import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Roboto+Mono:wght@400;500&family=Inter:wght@400;500;600;700&display=swap');

/* Professional Legal Contract Theme */
.report-wrapper { 
    font-family: 'Merriweather', Georgia, serif; 
    color: #0f172a; 
    background: white; 
    line-height: 1.8;
    font-size: 11pt;
}

/* Document Header */
.report-header { 
    text-align: center;
    padding: 50px 60px 30px; 
    border-bottom: 3px double {{ $brandColor }};
    margin-bottom: 30px;
}

.brand-logo { 
    height: 50px; 
    width: auto; 
    object-fit: contain; 
    margin-bottom: 15px; 
}

.report-header h1 { 
    font-size: 1.8rem; 
    font-weight: 700; 
    text-transform: uppercase; 
    letter-spacing: 0.08em;
    margin-bottom: 5px;
    color: #0f172a;
}

.report-header .contract-subtitle {
    font-size: 1rem;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: {{ $brandColor }};
    margin-bottom: 20px;
}

.report-meta { 
    font-family: 'Roboto Mono', monospace;
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 15px;
}

.report-meta .contract-number {
    font-weight: 600;
    color: #0f172a;
}

/* Main Content */
.report-content { 
    padding: 0 60px 60px; 
    font-size: 11pt;
}

/* Parties Section - Modern Card-Based Layout */
.parties-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin: 40px 0;
    page-break-inside: avoid;
}

.party-block {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.party-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid {{ $brandColor }};
}

.party-block h3 {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: {{ $brandColor }};
    margin: 0;
}

.party-designation {
    font-size: 0.7rem;
    color: #64748b;
    font-style: italic;
    font-weight: 400;
}

.party-fields {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.party-row {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.party-label {
    font-family: 'Inter', sans-serif;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
}

.party-value {
    font-size: 0.9rem;
    color: #0f172a;
    min-height: 1.5em;
    padding: 6px 0;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 400;
}

.party-value:empty::before {
    content: '—';
    color: #cbd5e1;
}

/* Recitals / Whereas */
.recitals {
    margin: 30px 0;
    padding: 20px 25px;
    background: linear-gradient(to right, {{ $brandColor }}08, transparent);
    border-left: 4px solid {{ $brandColor }};
    font-style: italic;
}

.recitals p {
    margin-bottom: 1rem;
}

.recitals p:last-child {
    margin-bottom: 0;
}

.therefore-clause {
    font-weight: 700;
    font-style: normal;
    margin-top: 1.5rem !important;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.02em;
}

/* Article Headers */
h2, .article-header { 
    font-family: 'Inter', sans-serif;
    font-size: 1.1rem; 
    text-transform: uppercase; 
    letter-spacing: 0.05em;
    border-bottom: 2px solid {{ $brandColor }}; 
    padding-bottom: 8px; 
    margin-top: 45px;
    margin-bottom: 20px;
    font-weight: 700;
    color: #0f172a;
}

h2::before, .article-header::before {
    content: "◆ ";
    color: {{ $brandColor }};
}

/* Section Headers */
h3, .section-header { 
    font-family: 'Inter', sans-serif;
    font-size: 0.95rem; 
    font-weight: 600;
    margin-top: 25px;
    margin-bottom: 12px;
    color: #1e293b;
}

/* Subsection Headers */
h4, .subsection-header {
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    margin-top: 18px;
    margin-bottom: 8px;
    color: #334155;
}

/* Paragraphs */
p { 
    margin-bottom: 1rem; 
    text-align: justify; 
    text-justify: inter-word;
}

/* Lists */
ul, ol {
    margin: 15px 0;
    padding-left: 25px;
}

li {
    margin-bottom: 8px;
    text-align: justify;
}

li::marker {
    color: {{ $brandColor }};
    font-weight: 700;
}
