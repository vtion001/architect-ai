{{-- Cover Letter - Header (Standard) --}}
<div class="cl-header">
    <div class="sender-info">
        <h1>{{ $senderName ?? 'Your Name' }}</h1>
        <p>{{ $senderTitle ?? 'Professional Role' }}</p>
        <div style="margin-top: 10px;">
            @if(!empty($contactInfo['email'])) <p>{{ $contactInfo['email'] }}</p> @endif
            @if(!empty($contactInfo['phone'])) <p>{{ $contactInfo['phone'] }}</p> @endif
            @if(!empty($contactInfo['location'])) <p>{{ $contactInfo['location'] }}</p> @endif
        </div>
    </div>
    <div class="cl-date">
        {{ now()->format('F j, Y') }}
    </div>
</div>
