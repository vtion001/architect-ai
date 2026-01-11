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

    @else
        {{-- Standard / Technical Layout (Top-down) --}}
        <div class="cv-header">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 30px;">
                <div style="flex: 1;">
                    <h1>{{ $recipientName ?? 'Candidate Name' }}</h1>
                    <div class="role">{{ $recipientTitle ?? 'Professional Role' }}</div>
                    <div class="contact-info">
                        @if(!empty($contactInfo['email'])) <span><strong style="margin-right:4px;">Email:</strong> {{ $contactInfo['email'] }}</span> @endif
                        @if(!empty($contactInfo['phone'])) <span><strong style="margin-right:4px;">Phone:</strong> {{ $contactInfo['phone'] }}</span> @endif
                        @if(!empty($contactInfo['location'])) <span><strong style="margin-right:4px;">Address:</strong> {{ $contactInfo['location'] }}</span> @endif
                    </div>
                </div>
                
                @if(isset($profilePhotoUrl) && $profilePhotoUrl)
                    <img src="{{ $profilePhotoUrl }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: {{ $variant === 'cv-technical' ? '12px' : '50%' }}; border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                @elseif(isset($logoUrl) && $logoUrl)
                    <img src="{{ $logoUrl }}" style="width: 80px; height: 80px; object-fit: contain;">
                @endif
            </div>

            @if(!empty($personalInfo) && array_filter($personalInfo))
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); font-size: 0.8rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                    @foreach(['Age' => 'age', 'DOB' => 'dob', 'Gender' => 'gender', 'Status' => 'civil_status', 'Nationality' => 'nationality', 'Religion' => 'religion'] as $label => $key)
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
    @endif

@endsection
