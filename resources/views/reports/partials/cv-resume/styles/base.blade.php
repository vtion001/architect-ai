{{-- CV Resume - Base Styles --}}
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700&family=Roboto+Mono:wght@400;500&display=swap');

/* Base CV Styles */
.report-wrapper { 
    font-family: 'Inter', sans-serif; 
    color: #334155; 
    line-height: 1.6;
    padding: 0 !important;
    display: flex;
    flex-direction: column;
}

/* Global Content Spacing */
.cv-body p, .cv-content p { margin-bottom: 1rem; }
.cv-body ul, .cv-content ul { margin-bottom: 1rem; padding-left: 1.5rem; }
.cv-body li, .cv-content li { margin-bottom: 0.5rem; }
.cv-body h2, .cv-content h2 { margin-top: 2rem; margin-bottom: 1rem; }
.cv-body h3, .cv-content h3 { margin-top: 1.5rem; margin-bottom: 0.25rem; }

@media print {
    .tailoring-report { display: none !important; }
    .report-wrapper { box-shadow: none; }
}

/* Common Utilities */
.contact-info {
    font-size: 0.85rem;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 10px;
    opacity: 0.8;
}
.contact-info span { display: flex; align-items: center; gap: 5px; }
