@extends('reports.layout')

@section('title', 'Cover Letter')
@section('container_class', 'cover-letter')

@section('styles')
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Merriweather:wght@300;400;700&display=swap');

    .report-wrapper {
        font-family: 'Inter', sans-serif;
        color: #334155;
        line-height: 1.8;
        padding: 60px !important;
        background: white;
    }

    /* Common Elements */
    .cl-header { margin-bottom: 40px; }
    .cl-date { margin-bottom: 20px; font-size: 0.9rem; color: #64748b; }
    .cl-recipient { margin-bottom: 30px; font-size: 0.95rem; }
    .cl-body { font-size: 1rem; color: #1e293b; white-space: pre-wrap; }
    .cl-body p { margin-bottom: 1.2rem; }
    .cl-signoff { margin-top: 40px; }
    .cl-signature { font-family: 'Merriweather', serif; font-style: italic; font-size: 1.2rem; margin-bottom: 5px; color: {{ $brandColor }}; }

    /* Variant: Standard Professional */
    @if($variant === 'cl-standard')
        .report-wrapper { font-family: 'Merriweather', serif; font-size: 0.95rem; }
        .cl-header { 
            border-bottom: 1px solid #e2e8f0; 
            padding-bottom: 20px; 
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .sender-info h1 { font-size: 1.5rem; color: #0f172a; font-weight: 700; margin-bottom: 5px; }
        .sender-info p { font-size: 0.85rem; color: #64748b; margin: 0; font-family: 'Inter', sans-serif; }
    @endif

    /* Variant: Modern Creative */
    @if($variant === 'cl-creative')
        .cl-header {
            background: #f8fafc;
            margin: -60px -60px 40px -60px; /* Bleed out */
            padding: 60px;
            border-bottom: 4px solid {{ $brandColor }};
        }
        .sender-info h1 { font-size: 2.5rem; font-weight: 900; color: #0f172a; letter-spacing: -0.03em; line-height: 1; }
        .sender-info .role { color: {{ $brandColor }}; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em; margin-top: 5px; }
        .cl-contact-row { display: flex; gap: 20px; margin-top: 15px; font-size: 0.85rem; color: #64748b; }
        .cl-contact-row span { display: flex; align-items: center; gap: 6px; }
    @endif
@endsection

@section('content')

    @if($variant === 'cl-creative')
        {{-- Modern Creative Layout --}}
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
    @else
        {{-- Standard Professional Layout --}}
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
    @endif

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

@endsection
