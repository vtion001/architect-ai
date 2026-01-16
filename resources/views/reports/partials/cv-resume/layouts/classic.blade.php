{{-- CV Resume - Classic Professional Layout (Centered) --}}
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
