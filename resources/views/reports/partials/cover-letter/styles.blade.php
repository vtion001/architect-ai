{{-- Cover Letter - Styles --}}
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Merriweather:wght@300;400;700&display=swap');

.report-wrapper {
    font-family: 'Inter', sans-serif;
    color: #334155;
    line-height: 1.8;
    padding: 60px !important;
    background: white;
}

/* Common Elements */
.cl-header { margin-bottom: 40px; }
.cl-date { margin-bottom: 20px; font-size: 0.9rem; color: #64748b; }
.cl-recipient { margin-bottom: 30px; font-size: 0.95rem; }
.cl-body { font-size: 1rem; color: #1e293b; white-space: pre-wrap; }
.cl-body p { margin-bottom: 1.2rem; }
.cl-signoff { margin-top: 40px; }
.cl-signature { font-family: 'Merriweather', serif; font-style: italic; font-size: 1.2rem; margin-bottom: 5px; color: {{ $brandColor }}; }

/* Variant: Standard Professional */
@if($variant === 'cl-standard')
    .report-wrapper { font-family: 'Merriweather', serif; font-size: 0.95rem; }
    .cl-header { 
        border-bottom: 1px solid #e2e8f0; 
        padding-bottom: 20px; 
        margin-bottom: 40px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .sender-info h1 { font-size: 1.5rem; color: #0f172a; font-weight: 700; margin-bottom: 5px; }
    .sender-info p { font-size: 0.85rem; color: #64748b; margin: 0; font-family: 'Inter', sans-serif; }
@endif

/* Variant: Modern Creative */
@if($variant === 'cl-creative')
    .cl-header {
        background: #f8fafc;
        margin: -60px -60px 40px -60px; /* Bleed out */
        padding: 60px;
        border-bottom: 4px solid {{ $brandColor }};
    }
    .sender-info h1 { font-size: 2.5rem; font-weight: 900; color: #0f172a; letter-spacing: -0.03em; line-height: 1; }
    .sender-info .role { color: {{ $brandColor }}; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em; margin-top: 5px; }
    .cl-contact-row { display: flex; gap: 20px; margin-top: 15px; font-size: 0.85rem; color: #64748b; }
    .cl-contact-row span { display: flex; align-items: center; gap: 6px; }
@endif
