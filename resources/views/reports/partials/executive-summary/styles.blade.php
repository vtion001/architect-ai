{{-- Executive Summary - Styles --}}
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@800;900&display=swap');

/* Global Infrastructure Styles */
.report-wrapper { font-family: 'Inter', sans-serif; color: #1e293b; background: white; }
.report-header { 
    background: #050505; 
    color: white; 
    padding: 60px 50px; 
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.report-header::after {
    content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background-image: radial-gradient(circle at top right, {{ $brandColor }}1A, transparent 70%);
    pointer-events: none;
    z-index: 1;
}
.header-content {
    position: relative;
    z-index: 10;
    max-width: 70%;
}
.brand-logo {
    position: relative;
    z-index: 10;
    width: 80px;
    height: 80px;
    object-fit: contain;
}
.report-header h1 { 
    font-family: 'Montserrat', sans-serif; 
    margin: 0; 
    font-size: 3rem; 
    font-weight: 900; 
    text-transform: uppercase;
    letter-spacing: -0.05em;
    line-height: 0.9;
}
.report-meta { 
    margin-top: 20px; 
    font-size: 0.75rem; 
    font-weight: 700; 
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: {{ $brandColor }};
}

.report-content { padding: 60px 50px; flex: 1; }

/* Blueprint Headers */
h2 { 
    color: #0f172a; 
    font-family: 'Montserrat', sans-serif;
    font-weight: 900;
    text-transform: uppercase;
    padding-bottom: 12px; 
    margin-top: 50px; 
    font-size: 1.25rem; 
    letter-spacing: 0.05em;
    border-bottom: 4px solid {{ $brandColor }};
    display: inline-block;
}
h3 { color: #334155; margin-top: 30px; font-weight: 700; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.02em; }
p { margin: 20px 0; color: #475569; line-height: 1.8; text-align: justify; }

ul, ol { margin: 20px 0; padding-left: 25px; color: #475569; }
li { margin-bottom: 12px; line-height: 1.6; }

/* Industrial Components */
.callout { 
    background: #f8fafc; 
    border-left: 5px solid {{ $brandColor }}; 
    padding: 30px; 
    margin: 40px 0; 
    font-style: italic; 
    font-weight: 500;
    color: #1e293b;
}

table { width: 100%; border-collapse: collapse; margin: 40px 0; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
th { background: #f1f5f9; padding: 15px; text-align: left; font-size: 0.7rem; font-weight: 900; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }

.footer { 
    padding: 40px 50px; 
    border-top: 1px solid #f1f5f9; 
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #94a3b8; 
    font-size: 0.7rem; 
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Minimal Variant */
@if($variant === 'exec-minimal')
    .report-header { background: white; color: #0f172a; padding: 60px 0; margin: 0 50px; border-bottom: 1px solid #e2e8f0; }
    .report-header h1 { font-size: 2.5rem; color: #0f172a; }
    .report-header::after { display: none; }
    h2 { border-bottom: none; border-left: 6px solid {{ $brandColor }}; padding-left: 20px; padding-bottom: 0; }
@endif

/* Detailed Variant */
@if($variant === 'exec-detailed')
    .report-header { padding: 100px 50px; background: linear-gradient(to bottom, #050505, #1e293b); }
    .report-header h1 { font-size: 4rem; }
    .report-content { background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 30px 30px; }
    .report-content > * { position: relative; z-index: 10; }
@endif
