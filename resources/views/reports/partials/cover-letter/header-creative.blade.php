{{-- Cover Letter - Header (Creative) --}}
<div class="cl-header">
    <div class="sender-info">
        <h1>{{ $senderName ?? 'Your Name' }}</h1>
        <div class="role">{{ $senderTitle ?? 'Professional Role' }}</div>
        <div class="cl-contact-row">
            @if(!empty($contactInfo['email'])) <span>{{ $contactInfo['email'] }}</span> @endif
            @if(!empty($contactInfo['phone'])) <span>{{ $contactInfo['phone'] }}</span> @endif
            @if(!empty($contactInfo['location'])) <span>{{ $contactInfo['location'] }}</span> @endif
            @if(!empty($contactInfo['website'])) <span>{{ $contactInfo['website'] }}</span> @endif
        </div>
    </div>
</div>
