{{-- CV Resume - International Standard Variant Styles (Healthcare/MLS) --}}
.report-wrapper {
    font-family: 'Inter', 'Times New Roman', serif;
    font-size: 11pt;
    line-height: 1.6;
    color: #1e293b;
    background: white;
    padding: 0;
}
.cv-header-intl {
    display: flex;
    gap: 30px;
    padding: 40px 50px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 4px solid {{ $brandColor }};
}
.cv-header-intl .photo-container {
    flex-shrink: 0;
}
.cv-header-intl .photo-placeholder {
    width: 120px;
    height: 150px;
    background: #e2e8f0;
    border: 3px solid {{ $brandColor }};
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    color: #94a3b8;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.cv-header-intl .photo-placeholder img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 5px;
}
.cv-header-intl .header-info {
    flex: 1;
}
.cv-header-intl h1 {
    font-family: 'Inter', sans-serif;
    font-size: 22pt;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0 0 15px 0;
    color: #0f172a;
    border-bottom: 2px solid {{ $brandColor }};
    padding-bottom: 8px;
    display: inline-block;
}
.cv-header-intl .contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px 20px;
    font-size: 10pt;
}
.cv-header-intl .contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    border-bottom: 1px solid #e2e8f0;
}
.cv-header-intl .contact-item .label {
    font-weight: 600;
    color: #64748b;
    min-width: 80px;
    text-transform: uppercase;
    font-size: 8pt;
    letter-spacing: 0.05em;
}
.cv-header-intl .contact-item .value {
    color: #1e293b;
    font-weight: 500;
}

.cv-content-intl {
    padding: 30px 50px 50px;
}

h2.section-title {
    font-family: 'Inter', sans-serif;
    font-size: 12pt;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin: 30px 0 15px 0;
    padding: 8px 15px;
    background: linear-gradient(90deg, {{ $brandColor }}15, transparent);
    border-left: 4px solid {{ $brandColor }};
    color: #0f172a;
}

.profile-summary {
    margin-left: 20px;
    margin-bottom: 20px;
    font-size: 11pt;
}
.profile-summary > li {
    list-style-type: disc;
    margin-bottom: 8px;
    line-height: 1.7;
}
.profile-summary li ul {
    margin-top: 5px;
    margin-left: 25px;
}
.profile-summary li li {
    list-style-type: circle;
    margin-bottom: 4px;
    color: #475569;
}

.education-block {
    margin-bottom: 20px;
    padding: 15px 20px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
.education-block .dates {
    font-weight: 700;
    color: {{ $brandColor }};
    font-size: 10pt;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 5px;
}
.education-block .institution {
    text-align: center;
    font-size: 11pt;
    font-weight: 600;
    color: #334155;
    margin: 5px 0;
}
.education-block .degree {
    text-align: center;
    font-size: 12pt;
    font-weight: 700;
    color: #0f172a;
    padding: 8px;
    background: white;
    border-radius: 4px;
    margin-top: 8px;
    border: 1px solid #e2e8f0;
}

.facility-block {
    margin-bottom: 30px;
    padding: 20px;
    background: #fafafa;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    page-break-inside: avoid;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.facility-block .facility-name {
    font-weight: 800;
    font-size: 13pt;
    color: #0f172a;
    border-bottom: 2px solid {{ $brandColor }};
    padding-bottom: 5px;
    margin-bottom: 10px;
}
.facility-block .facility-dates {
    font-weight: 700;
    color: {{ $brandColor }};
    font-size: 10pt;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-bottom: 5px;
}
.facility-block .facility-location,
.facility-block .facility-website {
    font-size: 10pt;
    color: #64748b;
    margin-bottom: 3px;
}
.facility-block .facility-website a {
    color: {{ $brandColor }};
    text-decoration: none;
}
.facility-block .facility-description {
    font-weight: 600;
    font-style: italic;
    color: #475569;
    margin: 15px 0 10px 0;
    padding: 10px;
    background: white;
    border-radius: 6px;
    border-left: 3px solid {{ $brandColor }};
    font-size: 10pt;
}
.facility-block .job-details {
    margin: 15px 0;
    padding: 10px 15px;
    background: white;
    border-radius: 6px;
}
.facility-block .job-details p {
    margin: 5px 0;
    font-size: 10pt;
}
.facility-block .job-details strong {
    font-weight: 700;
    color: #334155;
}

.responsibility-list,
.samples-list,
.equipment-list {
    margin: 10px 0 15px 25px;
}
.responsibility-list li,
.samples-list li,
.equipment-list li {
    list-style-type: disc;
    margin-bottom: 6px;
    padding: 4px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 10pt;
    color: #475569;
}

.certifications-block {
    margin-top: 20px;
    padding: 15px 20px;
    background: linear-gradient(135deg, {{ $brandColor }}08, {{ $brandColor }}15);
    border-radius: 10px;
    border: 1px solid {{ $brandColor }}30;
}
.certifications-block li {
    list-style-type: none;
    margin-bottom: 8px;
    padding: 8px 12px;
    background: white;
    border-radius: 6px;
    border-left: 3px solid {{ $brandColor }};
    font-size: 10pt;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.signature-block {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid #e2e8f0;
}
.signature-block p {
    margin: 10px 0;
    font-size: 11pt;
}
.signature-block .sign-line,
.signature-block .date-line {
    border-bottom: 2px solid #1e293b;
    display: inline-block;
    min-width: 200px;
    margin-left: 10px;
}
.signature-block .date-line {
    min-width: 120px;
}
