<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArchitGrid | The OS for High-Growth Agencies</title>
    <meta name="description" content="Stop trading time for money. The first AI operating system designed to help agencies scale from 10 to 100+ clients without hiring more staff.">
    <link rel="icon" type="image/png" href="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600" rel="stylesheet" />
    
    <!-- Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --bg: #030712;
            --card: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.08);
            --accent: #22d3ee;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg);
            color: #F8FAFC; 
            overflow-x: hidden; 
        }

        h1, h2, h3, h4, .heading { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Loader */
        .loader-overlay {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .loader-bar-bg {
            width: 200px;
            height: 2px;
            background: rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
            border-radius: 2px;
        }
        .loader-progress {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            background: var(--accent);
            width: 0%;
        }

        /* Marquee */
        .marquee-container {
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        }
        .marquee-content {
            display: flex;
            gap: 4rem;
            width: max-content;
        }

        /* Glass */
        .glass {
            background: var(--card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
        }
        .glass-hover:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(34,211,238,0.2);
        }
        
        /* Smooth Gradient Text */
        .gradient-text {
            background: linear-gradient(to right, #22d3ee, #3b82f6, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: gradient-move 5s linear infinite;
        }
        @keyframes gradient-move { 0% { background-position: 0% 50%; } 100% { background-position: 200% 50%; } }

        /* Grid Background */
        .bg-grid {
            background-image: linear-gradient(to right, #ffffff05 1px, transparent 1px),
                              linear-gradient(to bottom, #ffffff05 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(circle at center, black, transparent 80%);
        }

        /* 3D Tilt */
        .tilt-card {
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        /* Spotlight Effect */
        .bento-card, .glass {
            position: relative;
        }
        .bento-card::after, .glass::after {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(800px circle at var(--mouse-x) var(--mouse-y), rgba(34, 211, 238, 0.15), transparent 40%);
            opacity: 0;
            transition: opacity 0.5s;
            pointer-events: none;
            z-index: 50;
        }
        .bento-card:hover::after, .glass:hover::after {
            opacity: 1;
        }
    </style>
</head>
<body class="antialiased selection:bg-cyan-500/30 selection:text-cyan-200">

    <!-- Preloader -->
    <div class="loader-overlay">
        <div class="text-xs font-mono text-cyan-500 mb-4 tracking-[0.2em] uppercase">System Boot</div>
        <div class="loader-bar-bg"><div class="loader-progress"></div></div>
        <div class="counter text-4xl font-bold mt-4 text-white font-mono">0%</div>
    </div>

    <!-- Nav -->
    <nav class="fixed top-0 w-full z-50 border-b border-white/5 bg-[#030712]/80 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/20 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-bold text-lg tracking-tight">ArchitGrid</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-400">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#workflow" class="hover:text-white transition-colors">Workflow</a>
                <a href="#pricing" class="hover:text-white transition-colors">Pricing</a>
            </div>
            <a href="/waitlist" class="magnetic-btn px-5 py-2.5 rounded-lg bg-white text-slate-950 text-sm font-bold hover:scale-105 transition-transform">Get Access</a>
        </div>
    </nav>

    <!-- Hero -->
    <header class="relative pt-32 pb-20 lg:pt-48 overflow-hidden min-h-screen flex flex-col justify-center" id="hero">
        <!-- Backgrounds -->
        <div class="absolute inset-0 bg-grid pointer-events-none z-0"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-cyan-500/10 blur-[120px] rounded-full pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-950/30 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-wider mb-8 opacity-0 hero-fade">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                v2.0 Now Available
            </div>

            <h1 class="text-6xl md:text-8xl font-bold tracking-tight mb-8 leading-[1.1]">
                <div class="overflow-hidden"><span class="hero-text block translate-y-full">The Operating System</span></div>
                <div class="overflow-hidden"><span class="hero-text block translate-y-full text-slate-500">for <span class="gradient-text">High-Scale Agencies.</span></span></div>
            </h1>

            <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed opacity-0 hero-fade">
                Centralize your entire agency. Research, content generation, and client approvals in one unified workspace.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20 opacity-0 hero-fade">
                <a href="/waitlist" class="magnetic-btn h-14 px-8 rounded-xl bg-cyan-500 text-slate-950 font-bold flex items-center gap-2 hover:bg-cyan-400 transition-all shadow-[0_0_30px_rgba(34,211,238,0.3)] hover:scale-105">
                    Start Scaling
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <button onclick="openVideoModal()" class="magnetic-btn h-14 px-8 rounded-xl bg-white/5 border border-white/10 text-white font-medium hover:bg-white/10 transition-all flex items-center gap-2">
                    <i data-lucide="play-circle" class="w-4 h-4 text-slate-400"></i>
                    Watch Demo
                </button>
            </div>

            <!-- 3D Interactive Mockup -->
            <div class="relative mx-auto max-w-6xl opacity-0 hero-dashboard-container perspective-1000">
                <div class="tilt-inner transform-style-3d transition-transform duration-100 ease-out">
                    <div class="rounded-xl bg-[#1e293b] p-2 ring-1 ring-white/10 shadow-2xl relative z-10">
                        <div class="flex items-center gap-2 px-4 py-2 border-b border-white/5 bg-[#0f172a] rounded-t-lg">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500/50"></div>
                            </div>
                            <div class="mx-auto text-[10px] text-slate-500 font-mono">dashboard.architgrid.com</div>
                        </div>
                        <div class="relative aspect-[16/9] overflow-hidden rounded-b-lg bg-slate-900 group">
                            <!-- Dashboard Image Placeholder -->
                            <img src="/images/dashboard-ui.png" 
                                 class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-1000" alt="ArchitGrid AI Dashboard">
                        </div>
                    </div>

                    <!-- Floating Parallax Elements -->
                    <div class="absolute top-20 -left-12 glass p-4 rounded-xl shadow-2xl border-l-4 border-l-cyan-400 z-20 hero-float">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                                <i data-lucide="trending-up" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Client Growth</div>
                                <div class="text-xl font-bold text-white">+142%</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-20 -right-12 glass p-4 rounded-xl shadow-2xl border-l-4 border-l-purple-500 z-20 hero-float">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Auto-Scheduled</div>
                                <div class="text-xl font-bold text-white">42 Posts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Infinite Marquee -->
    <section class="py-12 border-y border-white/5 bg-white/[0.01] overflow-hidden marquee-container">
        <div class="flex marquee-wrapper">
            <!-- Duplicated content for seamless loop -->
            <div class="marquee-content px-8 opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="triangle" class="fill-white"></i> VANGUARD</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="circle" class="fill-white"></i> NEXUS</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="square" class="fill-white"></i> STRATOS</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="hexagon" class="fill-white"></i> ELEVATE</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="diamond" class="fill-white"></i> HORIZON</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="triangle" class="fill-white"></i> VANGUARD</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="circle" class="fill-white"></i> NEXUS</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="square" class="fill-white"></i> STRATOS</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="hexagon" class="fill-white"></i> ELEVATE</span>
                <span class="text-2xl font-black text-white flex items-center gap-2"><i data-lucide="diamond" class="fill-white"></i> HORIZON</span>
            </div>
        </div>
    </section>

    <!-- Bento Grid Features (Value Prop) -->
    <section id="features" class="py-32 relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-bold text-white mb-6">Everything You Need to Dominate</h2>
                <p class="text-slate-400 text-lg">Replace your entire fragmented tech stack.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[300px]">
                
                <!-- Card 1: Brand DNA (Large) -->
                <div class="md:col-span-2 glass glass-hover rounded-3xl p-10 relative overflow-hidden group bento-card">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 blur-[80px] group-hover:bg-cyan-500/20 transition-all duration-500"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 bg-cyan-500/20 rounded-xl flex items-center justify-center text-cyan-400 mb-6">
                                <i data-lucide="fingerprint" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">Instant Brand DNA</h3>
                            <p class="text-slate-400 max-w-md">Our AI ingests your client's website and PDFs to create a "Living Brand Profile." It never sounds robotic.</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 rounded-full bg-cyan-950 text-cyan-400 text-xs font-mono border border-cyan-800">Tone: Witty</span>
                            <span class="px-3 py-1 rounded-full bg-cyan-950 text-cyan-400 text-xs font-mono border border-cyan-800">Voice: Professional</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Security -->
                <div class="md:col-span-1 glass glass-hover rounded-3xl p-8 flex flex-col justify-between group bento-card">
                    <div>
                        <i data-lucide="shield-check" class="w-10 h-10 text-emerald-400 mb-6"></i>
                        <h3 class="text-xl font-bold text-white mb-2">Bank-Grade Security</h3>
                        <p class="text-sm text-slate-400">Multi-tenant architecture ensures Client A's data never leaks to Client B.</p>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-widest">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> SOC-2 Ready
                    </div>
                </div>

                <!-- Card 3: Whitelabel -->
                <div class="md:col-span-1 glass glass-hover rounded-3xl p-8 flex flex-col justify-between group bento-card">
                    <div>
                        <i data-lucide="layout-template" class="w-10 h-10 text-purple-400 mb-6"></i>
                        <h3 class="text-xl font-bold text-white mb-2">Whitelabel Portals</h3>
                        <p class="text-sm text-slate-400">Give clients a professional dashboard on your own domain.</p>
                    </div>
                    <div class="h-16 bg-slate-800 rounded-lg border border-white/10 flex items-center justify-center text-xs text-slate-500">
                        portal.youragency.com
                    </div>
                </div>

                <!-- Card 4: Analytics (Large) -->
                <div class="md:col-span-2 glass glass-hover rounded-3xl p-10 relative overflow-hidden group bento-card">
                    <div class="flex flex-col md:flex-row items-center gap-10 h-full">
                        <div class="flex-1">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 mb-6">
                                <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">Automated ROI Reports</h3>
                            <p class="text-slate-400">Prove your value. Auto-send weekly reports showing content performance and engagement.</p>
                        </div>
                        <div class="flex-1 w-full bg-slate-950/50 rounded-xl border border-white/5 p-6 shadow-2xl transform rotate-3 group-hover:rotate-0 transition-all duration-500">
                            <div class="flex justify-between items-end mb-4">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase">Impressions</p>
                                    <p class="text-2xl font-bold text-white">124.5K</p>
                                </div>
                                <div class="text-green-400 text-sm font-bold">+24%</div>
                            </div>
                            <div class="flex gap-2 items-end h-24">
                                <div class="w-full bg-slate-800 rounded-t h-[40%]"></div>
                                <div class="w-full bg-slate-800 rounded-t h-[60%]"></div>
                                <div class="w-full bg-blue-500 rounded-t h-[80%] shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                                <div class="w-full bg-slate-800 rounded-t h-[50%]"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Workflow (Pin Scroll) -->
    <section id="workflow" class="py-32 relative border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-24">
                <h2 class="text-4xl font-bold text-white mb-4">The Workflow</h2>
                <p class="text-slate-400">Automate the boring stuff. Focus on strategy.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-20">
                <!-- Text Steps -->
                <div class="lg:w-1/2 space-y-40 py-20">
                    <div class="step-item opacity-20 transition-opacity duration-500" data-index="0">
                        <div class="text-5xl font-black text-cyan-500/20 mb-4">01</div>
                        <h3 class="text-2xl font-bold text-white mb-4">Ingest Brand DNA</h3>
                        <p class="text-slate-400 text-lg leading-relaxed">
                            Upload a client's URL. We extract fonts, colors, tone, and values to build a style guide instantly.
                        </p>
                    </div>
                    <div class="step-item opacity-20 transition-opacity duration-500" data-index="1">
                        <div class="text-5xl font-black text-purple-500/20 mb-4">02</div>
                        <h3 class="text-2xl font-bold text-white mb-4">Generate & Refine</h3>
                        <p class="text-slate-400 text-lg leading-relaxed">
                            Ask for "10 LinkedIn posts about AI." The engine uses the Brand DNA to write perfect copy.
                        </p>
                    </div>
                    <div class="step-item opacity-20 transition-opacity duration-500" data-index="2">
                        <div class="text-5xl font-black text-green-500/20 mb-4">03</div>
                        <h3 class="text-2xl font-bold text-white mb-4">Approval & Automate</h3>
                        <p class="text-slate-400 text-lg leading-relaxed">
                            One click to send for approval. One click to schedule. Zero friction.
                        </p>
                    </div>
                </div>

                <!-- Sticky Images -->
                <div class="hidden lg:block lg:w-1/2 relative">
                    <div class="sticky top-40 h-[400px]">
                        <!-- Visual 1 -->
                        <div class="visual-item absolute inset-0 glass rounded-2xl overflow-hidden border border-cyan-500/20 transform translate-y-10 opacity-0" id="visual-0">
                             <div class="p-8 bg-gradient-to-br from-cyan-900/20 to-transparent h-full flex flex-col items-center justify-center text-center">
                                <i data-lucide="scan-line" class="w-16 h-16 text-cyan-400 mb-6"></i>
                                <h4 class="text-xl font-bold text-white">Scanning Brand...</h4>
                                <div class="mt-4 w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full w-2/3 bg-cyan-400 animate-[pulse_1s_ease-in-out_infinite]"></div>
                                </div>
                             </div>
                        </div>
                        <!-- Visual 2 -->
                        <div class="visual-item absolute inset-0 glass rounded-2xl overflow-hidden border border-purple-500/20 transform translate-y-10 opacity-0" id="visual-1">
                             <div class="p-6 bg-gradient-to-br from-purple-900/20 to-transparent h-full flex flex-col justify-center">
                                <div class="space-y-4">
                                    <div class="p-4 bg-white/5 rounded-xl border border-white/5">
                                        <div class="h-2 w-20 bg-blue-500 rounded mb-2"></div>
                                        <div class="h-2 w-full bg-white/10 rounded mb-2"></div>
                                        <div class="h-2 w-3/4 bg-white/10 rounded"></div>
                                    </div>
                                </div>
                             </div>
                        </div>
                        <!-- Visual 3 -->
                        <div class="visual-item absolute inset-0 glass rounded-2xl overflow-hidden border border-green-500/20 transform translate-y-10 opacity-0" id="visual-2">
                             <div class="p-8 bg-gradient-to-br from-green-900/20 to-transparent h-full flex flex-col items-center justify-center text-center">
                                <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mb-6">
                                    <i data-lucide="check" class="w-10 h-10 text-green-400"></i>
                                </div>
                                <h4 class="text-2xl font-bold text-white">Approved</h4>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials (Wall of Love) -->
    <section class="py-32 border-t border-white/5 bg-white/[0.01]">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-white mb-16 text-center">Agencies Are Scaling Faster</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="glass p-8 rounded-2xl">
                    <p class="text-slate-300 text-sm leading-relaxed mb-6">"ArchitGrid replaced 4 other tools for us. We went from managing 5 clients to 20 in two months without hiring."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-700"></div>
                        <div>
                            <div class="text-white font-bold text-sm">Sarah Jenkins</div>
                            <div class="text-slate-500 text-xs">Founder, Apex Digital</div>
                        </div>
                    </div>
                </div>
                <div class="glass p-8 rounded-2xl">
                    <p class="text-slate-300 text-sm leading-relaxed mb-6">"The Brand DNA feature is magic. It actually captures our clients' voices perfectly. No more rewrites."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-700"></div>
                        <div>
                            <div class="text-white font-bold text-sm">Mike Ross</div>
                            <div class="text-slate-500 text-xs">CEO, Pearson Media</div>
                        </div>
                    </div>
                </div>
                <div class="glass p-8 rounded-2xl">
                    <p class="text-slate-300 text-sm leading-relaxed mb-6">"Finally, a tool that understands multi-tenant security. My enterprise clients feel safe."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-700"></div>
                        <div>
                            <div class="text-white font-bold text-sm">Elena Rodriguez</div>
                            <div class="text-slate-500 text-xs">Director, CloudScale</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-32 bg-[#05080f] border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Simple, Scalable Pricing</h2>
            <p class="text-slate-400 mb-12">Cancel anytime. No hidden fees.</p>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto text-left">
                <!-- Card 1 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <h3 class="text-xl font-bold text-white">Starter</h3>
                    <div class="my-4"><span class="text-4xl font-bold text-white">$19</span>/mo</div>
                    <p class="text-sm text-slate-400 mb-8">For solo freelancers.</p>
                    <ul class="space-y-3 mb-8 text-sm text-slate-300">
                        <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-cyan-400"></i> 3 Clients</li>
                        <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-cyan-400"></i> Basic AI Generation</li>
                    </ul>
                    <a href="/waitlist" class="block w-full py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-center rounded-xl transition-colors">Start Trial</a>
                </div>
                
                <!-- Card 2 (Featured) -->
                <div class="p-8 rounded-3xl bg-gradient-to-b from-cyan-900/20 to-slate-900 border border-cyan-500/50 hover:-translate-y-2 transition-transform duration-300 relative shadow-2xl shadow-cyan-900/20">
                    <div class="absolute top-0 right-0 px-4 py-1 bg-cyan-500 text-black text-xs font-bold rounded-bl-xl rounded-tr-2xl">POPULAR</div>
                    <h3 class="text-xl font-bold text-white">Agency</h3>
                    <div class="my-4"><span class="text-4xl font-bold text-white">$49</span>/mo</div>
                    <p class="text-sm text-cyan-100/60 mb-8">For scaling teams.</p>
                    <ul class="space-y-3 mb-8 text-sm text-white">
                        <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-cyan-400"></i> 15 Clients</li>
                        <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-cyan-400"></i> Whitelabel Portal</li>
                        <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-cyan-400"></i> Advanced Brand DNA</li>
                    </ul>
                    <a href="/waitlist" class="block w-full py-3 bg-cyan-500 hover:bg-cyan-400 text-black font-bold text-center rounded-xl transition-colors">Get Started</a>
                </div>

                <!-- Card 3 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <h3 class="text-xl font-bold text-white">Enterprise</h3>
                    <div class="my-4"><span class="text-4xl font-bold text-white">Custom</span></div>
                    <p class="text-sm text-slate-400 mb-8">For large networks.</p>
                    <ul class="space-y-3 mb-8 text-sm text-slate-300">
                        <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-cyan-400"></i> Unlimited Clients</li>
                        <li class="flex gap-2"><i data-lucide="check" class="w-4 h-4 text-cyan-400"></i> API Access</li>
                    </ul>
                    <a href="/waitlist" class="block w-full py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-center rounded-xl transition-colors">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-40 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-t from-cyan-900/20 to-transparent"></div>
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-5xl md:text-7xl font-bold text-white mb-8 tracking-tight">Ready to join the <br><span class="gradient-text">Top 1% of Agencies?</span></h2>
            <p class="text-xl text-slate-400 mb-12">Secure your spot in the beta. Slots are limited.</p>
            <a href="/waitlist" class="inline-flex h-16 px-10 rounded-2xl bg-white text-black font-bold text-lg items-center gap-3 hover:scale-105 transition-transform shadow-[0_0_40px_rgba(255,255,255,0.3)]">
                Claim Your Workspace
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 border-t border-white/5 bg-[#020617] text-center">
        <div class="max-w-7xl mx-auto px-6 flex flex-col items-center gap-6">
            <p class="text-slate-500 text-sm">&copy; 2026 ArchitGrid Inc.</p>
        </div>
    </footer>

    <!-- Video Modal -->
    <div id="demo-modal" class="fixed inset-0 z-[99999] bg-black/90 backdrop-blur-xl hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
        <div class="absolute top-8 right-8 cursor-pointer text-white/50 hover:text-white transition-colors" onclick="closeVideoModal()">
            <i data-lucide="x" class="w-8 h-8"></i>
        </div>
        <div class="w-full max-w-6xl aspect-video bg-black rounded-2xl overflow-hidden border border-white/10 shadow-2xl relative" onclick="event.stopPropagation()">
            <!-- Using the webp recording as the demo 'video' -->
            <img src="/videos/demo.webp" class="w-full h-full object-contain" alt="ArchitGrid Demo Walkthrough">
            
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                 <div class="px-4 py-2 bg-black/80 rounded-full text-xs font-mono text-white border border-white/20">
                     DEMO RECORDING
                 </div>
            </div>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            gsap.registerPlugin(ScrollTrigger);

            // 1. Loader
            const tlLoader = gsap.timeline({
                onComplete: () => {
                    document.querySelector('.loader-overlay').style.display = 'none';
                    initAnimations();
                }
            });

            let countObj = { val: 0 };
            tlLoader.to(countObj, {
                val: 100,
                duration: 1.5,
                ease: "power2.inOut",
                onUpdate: () => {
                    document.querySelector('.counter').innerText = Math.floor(countObj.val) + "%";
                    document.querySelector('.loader-progress').style.width = Math.floor(countObj.val) + "%";
                }
            })
            .to('.loader-overlay', {
                yPercent: -100,
                duration: 0.8,
                ease: "expo.inOut"
            });

            // 2. Animations
            function initAnimations() {
                // Hero Text Reveal
                gsap.to('.hero-text', {
                    y: 0,
                    duration: 1.2,
                    stagger: 0.1,
                    ease: "power4.out"
                });

                // Hero Fade
                gsap.to('.hero-fade', {
                    opacity: 1,
                    duration: 1,
                    stagger: 0.1,
                    delay: 0.5
                });

                // Dashboard Entry
                gsap.to('.hero-dashboard-container', {
                    opacity: 1,
                    y: 0,
                    duration: 1.5,
                    delay: 0.6,
                    ease: "power3.out"
                });

                // Marquee Loop
                gsap.to(".marquee-content", {
                    xPercent: -50,
                    repeat: -1,
                    duration: 20,
                    ease: "linear"
                });

                // Bento Grid Stagger
                gsap.from(".bento-card", {
                    scrollTrigger: {
                        trigger: "#features",
                        start: "top 80%"
                    },
                    y: 50,
                    opacity: 0,
                    duration: 0.8,
                    stagger: 0.1,
                    ease: "power2.out"
                });
            }

            // 3. 3D Tilt Effect (Enhanced with Parallax)
            const heroContainer = document.querySelector('.hero-dashboard-container');
            const tiltInner = document.querySelector('.tilt-inner');

            document.addEventListener('mousemove', (e) => {
                if (!heroContainer) return;
                
                const { clientX, clientY } = e;
                const { innerWidth, innerHeight } = window;
                
                // Calculate percentages (-1 to 1)
                const x = (clientX / innerWidth - 0.5) * 2;
                const y = (clientY / innerHeight - 0.5) * 2;

                // Dashboard Tilt
                gsap.to(tiltInner, {
                    rotationY: x * 5, // Max 5deg tilt
                    rotationX: -y * 5,
                    duration: 0.5,
                    ease: "power2.out"
                });

                // Parallax Floating Elements
                gsap.to('.hero-float', {
                    x: x * 30, // Increased movement
                    y: y * 30,
                    duration: 0.8,
                    ease: "power2.out"
                });
            });

            // 4. Magnetic Buttons & Spotlight Effect
            const buttons = document.querySelectorAll('.magnetic-btn');
            const cards = document.querySelectorAll('.bento-card, .glass');

            // Button Magnetism
            buttons.forEach(btn => {
                btn.addEventListener('mousemove', (e) => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    
                    gsap.to(btn, {
                        x: x * 0.3,
                        y: y * 0.3,
                        duration: 0.3,
                        ease: "power2.out"
                    });
                });

                btn.addEventListener('mouseleave', () => {
                    gsap.to(btn, { x: 0, y: 0, duration: 0.5, ease: "elastic.out(1, 0.3)" });
                });
            });

            // Card Spotlight
            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    card.style.setProperty('--mouse-x', `${x}px`);
                    card.style.setProperty('--mouse-y', `${y}px`);
                });
            });

            // 5. Workflow Pin-Scroll Logic
            const steps = document.querySelectorAll('.step-item');
            const visuals = document.querySelectorAll('.visual-item');

            gsap.set(visuals[0], { opacity: 1, y: 0 });
            gsap.set(steps[0], { opacity: 1 });

            steps.forEach((step, i) => {
                ScrollTrigger.create({
                    trigger: step,
                    start: "top center",
                    end: "bottom center",
                    onEnter: () => setActive(i),
                    onEnterBack: () => setActive(i)
                });
            });

            function setActive(index) {
                steps.forEach((s, i) => {
                    gsap.to(s, { opacity: i === index ? 1 : 0.2, duration: 0.3 });
                });
                visuals.forEach((v, i) => {
                    if (i === index) {
                        gsap.to(v, { opacity: 1, y: 0, duration: 0.5 });
                    } else {
                        gsap.to(v, { opacity: 0, y: 20, duration: 0.5 });
                    }
                });
            }

            // 6. Video Modal Logic
            window.openVideoModal = function() {
                const modal = document.getElementById('demo-modal');
                modal.classList.remove('hidden');
                // Small delay to allow display:block to apply before opacity transition
                setTimeout(() => modal.classList.add('opacity-100'), 10);
            }

            window.closeVideoModal = function() {
                const modal = document.getElementById('demo-modal');
                modal.classList.remove('opacity-100');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }
        });
    </script>
</body>
</html>
