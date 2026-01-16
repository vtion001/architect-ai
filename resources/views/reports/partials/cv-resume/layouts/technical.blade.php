{{-- CV Resume - Technical Layout (Top-down) --}}
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
