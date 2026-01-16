{{-- Cover Letter - Content Body --}}
@if($variant === 'cl-creative')
    <div class="cl-date" style="font-weight: 600; color: {{ $brandColor }};">
        {{ now()->format('F j, Y') }}
    </div>
@endif

@if($recipientName || $recipientTitle)
<div class="cl-recipient">
    @if($recipientName) <strong>{{ $recipientName }}</strong><br> @endif
    @if($recipientTitle) {{ $recipientTitle }}<br> @endif
    @if($companyAddress) {{ $companyAddress }}<br> @endif
</div>
@endif

<div class="cl-body">
    {!! $content !!}
</div>

<div class="cl-signoff">
    <p>Sincerely,</p>
    <div class="cl-signature">{{ $senderName ?? 'Your Name' }}</div>
</div>
