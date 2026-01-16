{{-- CV Resume - Technical Expert Variant Styles --}}
.report-wrapper { font-family: 'Roboto Mono', monospace; font-size: 0.85rem; color: #334155; }
.cv-header {
    background: #0f172a;
    color: white;
    padding: 60px 50px;
    border-left: 12px solid {{ $brandColor }};
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.cv-header h1 { 
    margin: 0; 
    font-size: 2.5rem; 
    font-weight: 700; 
    letter-spacing: -1px; 
    line-height: 1;
}
.cv-header .role { 
    color: {{ $brandColor }}; 
    font-weight: 500; 
    margin-top: 10px; 
    font-size: 1.1rem; 
    display: block;
}
.contact-info {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    font-size: 0.8rem;
    opacity: 0.8;
}
.cv-content { padding: 50px; }
h2 {
    background: #f1f5f9;
    padding: 8px 12px;
    font-size: 0.95rem;
    font-weight: 700;
    margin-top: 40px;
    margin-bottom: 20px;
    border-left: 4px solid {{ $brandColor }};
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #0f172a;
}
h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 5px;
    color: #0f172a;
}
.job-meta {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 15px;
    border-bottom: 1px dashed #cbd5e1;
    padding-bottom: 10px;
    display: inline-block;
    width: 100%;
}
/* Tech-specific tags */
ul li::before { content: "> "; color: {{ $brandColor }}; font-weight: bold; margin-right: 5px; }
ul { list-style: none; padding-left: 0; }
