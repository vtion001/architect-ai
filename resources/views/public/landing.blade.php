{{--
    ArchitGrid Public Landing Page
    
    Modularized landing page with GSAP animations and modern design.
    All sections extracted to partials for maintainability.
    
    Sections:
    - Preloader
    - Navigation
    - Hero with 3D Mockup
    - Infinite Marquee
    - Features Bento Grid
    - Workflow with Pin-Scroll
    - Testimonials
    - Pricing
    - Final CTA
    - Footer
    - Video Modal
--}}

<!DOCTYPE html>
<html lang="en">
    @include('public.partials.landing.head')
    
<body class="antialiased selection:bg-cyan-500/30 selection:text-cyan-200">

    {{-- Preloader --}}
    <div class="loader-overlay">
        <div class="text-xs font-mono text-cyan-500 mb-4 tracking-[0.2em] uppercase">System Boot</div>
        <div class="loader-bar-bg"><div class="loader-progress"></div></div>
        <div class="counter text-4xl font-bold mt-4 text-white font-mono">0%</div>
    </div>

    {{-- Navigation --}}
    @include('public.partials.landing.nav')

    {{-- Hero Section --}}
    @include('public.partials.landing.hero')

    {{-- Infinite Marquee --}}
    @include('public.partials.landing.marquee')

    {{-- Features Bento Grid --}}
    @include('public.partials.landing.features')

    {{-- Workflow Section --}}
    @include('public.partials.landing.workflow')

    {{-- Testimonials --}}
    @include('public.partials.landing.testimonials')

    {{-- Pricing --}}
    @include('public.partials.landing.pricing')

    {{-- Final CTA --}}
    @include('public.partials.landing.cta')

    {{-- Footer --}}
    @include('public.partials.landing.footer')

    {{-- Video Modal --}}
    @include('public.partials.landing.video-modal')

    {{-- JavaScript --}}
    @include('public.partials.landing.scripts')

</body>
</html>
