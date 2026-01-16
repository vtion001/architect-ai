{{-- CV Resume - International Standard Layout (Healthcare/MLS) --}}
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
