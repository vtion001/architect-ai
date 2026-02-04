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

/* Parties Section */
.parties-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin: 30px 0 40px;
    padding: 25px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
}

.party-block h3 {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: {{ $brandColor }};
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid {{ $brandColor }};
}

.party-block .party-field {
    display: flex;
    flex-wrap: nowrap;
    align-items: baseline;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.party-block .party-label {
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    color: #475569;
    min-width: 140px;
    white-space: nowrap;
    flex-shrink: 0;
}

.party-block .party-value {
    color: #0f172a;
    flex: 1;
    min-height: 1.2em;
    white-space: pre-wrap;
    padding-bottom: 2px;
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
