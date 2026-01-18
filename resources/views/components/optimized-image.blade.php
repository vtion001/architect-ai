{{--
    Optimized Image Component
    
    Usage:
    <x-optimized-image 
        src="https://res.cloudinary.com/..." 
        alt="Description"
        width="300"
        height="200"
        class="rounded-lg"
        :lazy="true"
    />
    
    Features:
    - Automatic WebP/AVIF format selection
    - Responsive srcset generation
    - Lazy loading by default
    - Blur placeholder for progressive loading
    - Proper width/height to prevent layout shift
--}}

@props([
    'src',
    'alt' => '',
    'width' => null,
    'height' => null,
    'class' => '',
    'lazy' => true,
    'responsive' => false,
    'sizes' => null,
    'placeholder' => false,
])

@php
    use App\Helpers\CloudinaryOptimizer;
    
    $isCloudinary = str_contains($src, 'cloudinary.com');
    $loading = $lazy ? 'lazy' : 'eager';
    $decoding = 'async';
    
    if ($isCloudinary) {
        if ($responsive) {
            $imageData = CloudinaryOptimizer::responsive($src, [320, 640, 768, 1024, 1280], [
                'sizes' => $sizes ?? '100vw',
            ]);
            $optimizedSrc = $imageData['src'];
            $srcset = $imageData['srcset'];
            $imageSizes = $imageData['sizes'];
        } else {
            $optimizedSrc = CloudinaryOptimizer::optimize($src, $width, $height);
            $srcset = null;
            $imageSizes = null;
        }
        
        $placeholderSrc = $placeholder ? CloudinaryOptimizer::placeholder($src) : null;
    } else {
        $optimizedSrc = $src;
        $srcset = null;
        $imageSizes = null;
        $placeholderSrc = null;
    }
@endphp

@if($isCloudinary && $responsive)
    {{-- Responsive image with srcset --}}
    <img 
        src="{{ $optimizedSrc }}"
        srcset="{{ $srcset }}"
        sizes="{{ $imageSizes }}"
        alt="{{ $alt }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        class="{{ $class }}"
        loading="{{ $loading }}"
        decoding="{{ $decoding }}"
        {{ $attributes }}
    >
@elseif($placeholder && $placeholderSrc)
    {{-- Progressive loading with blur placeholder --}}
    <div 
        class="relative {{ $class }}" 
        style="@if($width && $height) aspect-ratio: {{ $width }}/{{ $height }}; @endif"
        x-data="{ loaded: false }"
    >
        {{-- Blur placeholder --}}
        <img 
            src="{{ $placeholderSrc }}"
            alt=""
            class="absolute inset-0 w-full h-full object-cover filter blur-lg scale-105"
            aria-hidden="true"
            x-show="!loaded"
            x-transition:leave="transition-opacity duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
        
        {{-- Actual image --}}
        <img 
            src="{{ $optimizedSrc }}"
            alt="{{ $alt }}"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            class="w-full h-full object-cover"
            loading="{{ $loading }}"
            decoding="{{ $decoding }}"
            @load="loaded = true"
            {{ $attributes }}
        >
    </div>
@else
    {{-- Standard optimized image --}}
    <img 
        src="{{ $optimizedSrc }}"
        alt="{{ $alt }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        class="{{ $class }}"
        loading="{{ $loading }}"
        decoding="{{ $decoding }}"
        {{ $attributes }}
    >
@endif
