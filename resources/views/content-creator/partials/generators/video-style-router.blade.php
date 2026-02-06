{{--
    Video Style Router Component
    
    Dynamically loads the appropriate video style form based on videoStyle selection.
    Similar to template-form-router in document-builder.
--}}

<div class="video-style-router">
    {{-- UGC / Authentic Style --}}
    <div x-show="videoStyle === 'UGC'" x-transition:enter="transition ease-out duration-200">
        @include('content-creator.partials.generators.video-styles.ugc-authentic')
    </div>

    {{-- Cinematic Style --}}
    <div x-show="videoStyle === 'Cinematic'" x-transition:enter="transition ease-out duration-200">
        @include('content-creator.partials.generators.video-styles.cinematic')
    </div>

    {{-- 3D Animation Style --}}
    <div x-show="videoStyle === 'Animation'" x-transition:enter="transition ease-out duration-200">
        @include('content-creator.partials.generators.video-styles.animation')
    </div>

    {{-- Minimalist Style --}}
    <div x-show="videoStyle === 'Minimalist'" x-transition:enter="transition ease-out duration-200">
        @include('content-creator.partials.generators.video-styles.minimalist')
    </div>
</div>
