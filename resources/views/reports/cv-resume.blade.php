@extends('reports.layout')

@section('title', 'CV / Resume')
@section('container_class', 'cv-resume')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700&family=Roboto+Mono:wght@400;500&display=swap');

    /* Base CV Styles */
    .report-wrapper { 
        font-family: 'Inter', sans-serif; 
        color: #334155; 
        line-height: 1.6;
        padding: 0 !important; /* Reset default padding for full control */
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

    /* Variant: Classic Professional */
    @if($variant === 'cv-classic')
        .cv-header {
            text-align: center;
            padding: 50px 60px 30px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 40px;
        }
        .cv-header h1 {
            font-family: 'Merriweather', serif;
            font-size: 2.8rem;
            color: #1e293b;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        .cv-header .role {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: {{ $brandColor }};
            font-weight: 600;
            margin-bottom: 20px;
        }
        .contact-info {
            justify-content: center;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #64748b;
        }
        .cv-content { padding: 0 60px 50px; }
        h2 {
            font-family: 'Merriweather', serif;
            font-size: 1.25rem;
            color: #0f172a;
            border-bottom: 2px solid {{ $brandColor }};
            padding-bottom: 8px;
            margin-top: 40px;
            margin-bottom: 20px;
            display: inline-block;
            min-width: 200px;
        }
        h3 {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
        }
        .job-meta {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #94a3b8;
            font-style: italic;
            margin-bottom: 12px;
        }
        ul { padding-left: 1.2rem; }
        li { margin-bottom: 6px; color: #475569; }
    @endif

    /* Variant: Modern Creative */
    @if($variant === 'cv-modern')
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
    @endif

    /* Variant: Technical Expert */
    @if($variant === 'cv-technical')
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
    @endif

    /* Variant: International Standard (Healthcare/MLS) */
    @if($variant === 'cv-international')
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
    @endif

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
@endsection

@section('content')

    @if($variant === 'cv-modern')
        {{-- Modern Sidebar Layout --}}
        <div class="cv-sidebar">
            <div class="mb-8 text-center">
                @if(!empty($profilePhotoUrl))
                    <img src="{{ $profilePhotoUrl }}" style="width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 4px solid white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                @elseif(!empty($profile_photo_url))
                    <img src="{{ $profile_photo_url }}" style="width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 4px solid white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                @elseif(!empty($logoUrl))
                    <img src="{{ $logoUrl }}" style="width: 80px; height: 80px; object-fit: contain; opacity: 0.8;">
                @else
                    <div style="width: 100px; height: 100px; background: #e2e8f0; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold; color: #94a3b8;">
                        {{ substr($recipientName ?? 'U', 0, 1) }}
                    </div>
                @endif
            </div>

            <div class="sidebar-section">
                <h2>Contact</h2>
                <div style="font-size: 0.8rem; line-height: 1.6; display: flex; flex-direction: column; gap: 10px;">
                    @if(!empty($contactInfo['email'])) 
                        <div>
                            <strong style="display:block; font-size:0.7rem; text-transform:uppercase; color:#94a3b8;">Email</strong>
                            <span style="word-break: break-all;">{{ $contactInfo['email'] }}</span>
                        </div> 
                    @endif
                    @if(!empty($contactInfo['phone'])) 
                        <div>
                            <strong style="display:block; font-size:0.7rem; text-transform:uppercase; color:#94a3b8;">Phone</strong>
                            <span>{{ $contactInfo['phone'] }}</span>
                        </div> 
                    @endif
                    @if(!empty($contactInfo['location'])) 
                        <div>
                            <strong style="display:block; font-size:0.7rem; text-transform:uppercase; color:#94a3b8;">Address</strong>
                            <span>{{ $contactInfo['location'] }}</span>
                        </div> 
                    @endif
                    @if(!empty($contactInfo['website'])) 
                        <div>
                            <strong style="display:block; font-size:0.7rem; text-transform:uppercase; color:#94a3b8;">Link</strong>
                            <a href="{{ $contactInfo['website'] }}" target="_blank" style="color: {{ $brandColor }}; font-weight: 600;">{{ $contactInfo['website'] }}</a>
                        </div> 
                    @endif
                </div>
            </div>

            @if(!empty($personalInfo) && array_filter($personalInfo))
            <div class="sidebar-section">
                <h2>Professional Info</h2>
                <div style="font-size: 0.8rem; line-height: 1.6; display: grid; grid-template-columns: 1fr; gap: 8px;">
                    @foreach(['Age' => 'age', 'Date of Birth' => 'dob', 'Gender' => 'gender', 'Civil Status' => 'civil_status', 'Nationality' => 'nationality', 'Religion' => 'religion', 'Height' => 'height', 'Weight' => 'weight'] as $label => $key)
                        @if(!empty($personalInfo[$key]))
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 2px;">
                                <span style="color: #64748b; font-weight: 500;">{{ $label }}:</span>
                                <span style="font-weight: 600; text-transform: uppercase;">{{ $personalInfo[$key] }}</span>
                            </div>
                        @endif
                    @endforeach
                    
                    @if(!empty($personalInfo['place_of_birth']))
                        <div style="margin-top: 5px;">
                            <span style="color: #64748b; font-weight: 500; display: block;">Place of Birth:</span>
                            <span style="font-weight: 600; text-transform: uppercase;">{{ $personalInfo['place_of_birth'] }}</span>
                        </div>
                    @endif

                    @if(!empty($personalInfo['languages']))
                        <div style="margin-top: 5px;">
                            <span style="color: #64748b; font-weight: 500; display: block;">Languages:</span>
                            <span style="font-weight: 600; text-transform: uppercase;">{{ $personalInfo['languages'] }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="sidebar-section" style="margin-top: auto; opacity: 0.6; font-size: 0.7rem;">
                <p>Generated by ArchitectAI</p>
            </div>
        </div>

        <div class="cv-main">
            <div class="cv-header">
                <h1>{{ $recipientName ?? 'Candidate Name' }}</h1>
                <div class="role">{{ $recipientTitle ?? 'Professional Role' }}</div>
            </div>

            <div class="cv-body">
                {!! $content !!}
            </div>
        </div>

    @elseif($variant === 'cv-classic')
        {{-- Classic Professional Layout (Centered) --}}
        <div class="cv-header">
            @if(!empty($profilePhotoUrl) || !empty($profile_photo_url))
                <img src="{{ $profilePhotoUrl ?? $profile_photo_url }}" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; margin: 0 auto 20px; display: block; border: 4px solid white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            @elseif(!empty($logoUrl))
                <img src="{{ $logoUrl }}" style="width: 80px; height: 80px; object-fit: contain; margin: 0 auto 20px; display: block;">
            @else
                <div style="width: 120px; height: 120px; background: #f1f5f9; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; color: #cbd5e1; border: 4px solid white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); font-family: 'Merriweather', serif;">
                    {{ substr($recipientName ?? 'U', 0, 1) }}
                </div>
            @endif

            <h1>{{ $recipientName ?? 'Candidate Name' }}</h1>
            <div class="role">{{ $recipientTitle ?? 'Professional Role' }}</div>
            
            <div class="contact-info">
                @if(!empty($contactInfo['email'])) <span><strong style="margin-right:4px;">Email:</strong> {{ $contactInfo['email'] }}</span> @endif
                @if(!empty($contactInfo['phone'])) <span><strong style="margin-right:4px;">Phone:</strong> {{ $contactInfo['phone'] }}</span> @endif
                @if(!empty($contactInfo['location'])) <span><strong style="margin-right:4px;">Address:</strong> {{ $contactInfo['location'] }}</span> @endif
                @if(!empty($contactInfo['website'])) <span><strong style="margin-right:4px;">Web:</strong> <a href="{{ $contactInfo['website'] }}" style="color:inherit; text-decoration:none;">{{ $contactInfo['website'] }}</a></span> @endif
            </div>

            @if(!empty($personalInfo) && array_filter($personalInfo))
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); font-size: 0.8rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 15px;">
                    @foreach(['Age' => 'age', 'DOB' => 'dob', 'Gender' => 'gender', 'Status' => 'civil_status', 'Nationality' => 'nationality'] as $label => $key)
                        @if(!empty($personalInfo[$key]))
                            <div>
                                <span style="color: #64748b; font-weight: 600;">{{ $label }}:</span>
                                <span style="text-transform: uppercase;">{{ $personalInfo[$key] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="cv-content">
            {!! $content !!}
        </div>

    @elseif($variant === 'cv-international')
        {{-- International Standard Layout (Healthcare/MLS) --}}
        <div class="cv-header-intl">
            {{-- Photo Placeholder --}}
            <div class="photo-container">
                <div class="photo-placeholder">
                    @if(!empty($profilePhotoUrl))
                        <img src="{{ $profilePhotoUrl }}" alt="{{ $recipientName ?? 'Photo' }}">
                    @elseif(!empty($profile_photo_url))
                        <img src="{{ $profile_photo_url }}" alt="{{ $recipientName ?? 'Photo' }}">
                    @else
                        {{ substr($recipientName ?? 'U', 0, 1) }}
                    @endif
                </div>
            </div>

            {{-- Header Info --}}
            <div class="header-info">
                <h1>{{ $recipientName ?? 'CANDIDATE NAME' }}</h1>
                
                @if(!empty($recipientTitle))
                    <div style="font-size: 11pt; font-weight: 600; color: #475569; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.05em;">
                        {{ $recipientTitle }}
                    </div>
                @endif

                <div class="contact-grid">
                    <div class="contact-item">
                        <span class="label">Address</span>
                        <span class="value">{{ $contactInfo['location'] ?? 'Current Address' }}</span>
                    </div>
                    <div class="contact-item">
                        <span class="label">City</span>
                        <span class="value">{{ $personalInfo['city'] ?? 'City, State/Province, Country' }}</span>
                    </div>
                    <div class="contact-item">
                        <span class="label">Phone</span>
                        <span class="value">{{ $contactInfo['phone'] ?? '+1 (000) 000-0000' }}</span>
                    </div>
                    <div class="contact-item">
                        <span class="label">Alt Phone</span>
                        <span class="value">{{ $personalInfo['alternate_phone'] ?? 'N/A' }}</span>
                    </div>
                    <div class="contact-item" style="grid-column: span 2;">
                        <span class="label">Email</span>
                        <span class="value">{{ $contactInfo['email'] ?? 'email@domain.com' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="cv-content-intl">
            {!! $content !!}

            {{-- Signature Block --}}
            <div class="signature-block">
                <p>Sign<span class="sign-line"></span></p>
                <p>Date<span class="date-line"></span></p>
            </div>
        </div>


    @else
        {{-- Technical Layout (Top-down) --}}
        <div class="cv-header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 40px; width: 100%;">
                <div style="flex: 1; min-width: 0;">
                    <h1>{{ $recipientName ?? 'Candidate Name' }}</h1>
                    <div class="role">{{ $recipientTitle ?? 'Professional Role' }}</div>
                    <div class="contact-info">
                        @if(!empty($contactInfo['email'])) <span><strong style="margin-right:4px;">Email:</strong> {{ $contactInfo['email'] }}</span> @endif
                        @if(!empty($contactInfo['phone'])) <span><strong style="margin-right:4px;">Phone:</strong> {{ $contactInfo['phone'] }}</span> @endif
                        @if(!empty($contactInfo['location'])) <span><strong style="margin-right:4px;">Address:</strong> {{ $contactInfo['location'] }}</span> @endif
                        @if(!empty($contactInfo['website'])) <span><strong style="margin-right:4px;">Web:</strong> <a href="{{ $contactInfo['website'] }}" style="color:inherit; text-decoration:none;">{{ $contactInfo['website'] }}</a></span> @endif
                    </div>
                </div>
                
                <div style="flex-shrink: 0;">
                    @if(isset($profilePhotoUrl) && $profilePhotoUrl)
                        <img src="{{ $profilePhotoUrl }}" style="width: 110px; height: 110px; object-fit: cover; border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    @elseif(isset($profile_photo_url) && $profile_photo_url)
                        <img src="{{ $profile_photo_url }}" style="width: 110px; height: 110px; object-fit: cover; border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    @elseif(isset($logoUrl) && $logoUrl)
                        <img src="{{ $logoUrl }}" style="width: 80px; height: 80px; object-fit: contain; background: white; padding: 5px; border-radius: 8px;">
                    @else
                        <div style="width: 110px; height: 110px; background: rgba(255,255,255,0.1); border: 2px dashed rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold; color: rgba(255,255,255,0.4);">
                            {{ substr($recipientName ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            @if(!empty($personalInfo) && array_filter($personalInfo))
                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.15); font-size: 0.8rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                    @foreach(['Age' => 'age', 'DOB' => 'dob', 'Gender' => 'gender', 'Status' => 'civil_status', 'Nationality' => 'nationality', 'Religion' => 'religion'] as $label => $key)
                        @if(!empty($personalInfo[$key]))
                            <div>
                                <span style="color: rgba(255,255,255,0.6); font-weight: 500; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">{{ $label }}</span>
                                <span style="color: white; font-weight: 600;">{{ $personalInfo[$key] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="cv-content">
            {!! $content !!}
        </div>
    @endif

@endsection
