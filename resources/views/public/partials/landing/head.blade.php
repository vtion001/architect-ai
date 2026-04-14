<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArchitGrid | The OS for High-Growth Agencies</title>
    <meta name="description" content="Stop trading time for money. The first AI operating system designed to help agencies scale from 10 to 100+ clients without hiring more staff.">
    <link rel="icon" type="image/png" href="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600" rel="stylesheet" />
    
    <!-- Font preload for critical weights -->
    <link rel="preload" href="https://fonts.bunny.net/figtree/files/figtree-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.bunny.net/figtree/files/figtree-latin-600-normal.woff2" as="font" type="font/woff2" crossorigin>

    <!-- Vite bundle (Alpine + Lucide + Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GSAP (deferred — does not block rendering) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>

    @include('public.partials.landing.styles')
</head>
