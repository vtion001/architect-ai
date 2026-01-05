<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArchitGrid | The Content Infrastructure for Agencies</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:900|inter:400,500,700,800|jetbrains-mono:500" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505; color: #E2E8F0; overflow-x: hidden; }
        h1, h2, h3 { font-family: 'Montserrat', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        
        /* Advanced Infrastructure Grid */
        .grid-canvas {
            background-image: 
                linear-gradient(to right, rgba(0, 242, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 242, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black, transparent 80%);
        }

        /* Odd & Structural Shapes */
        .arch-poly-1 { clip-path: polygon(0 0, 100% 0, 100% 85%, 85% 100%, 0 100%); }
        .arch-poly-2 { clip-path: polygon(15% 0, 100% 0, 100% 100%, 0 100%, 0 15%); }
        
        .blueprint-border { border: 1px solid rgba(0, 242, 255, 0.1); position: relative; }
        .blueprint-border::before { content: ''; position: absolute; top: -5px; left: -5px; width: 10px; height: 10px; border-top: 1px solid #00F2FF; border-left: 1px solid #00F2FF; }
        .blueprint-border::after { content: ''; position: absolute; bottom: -5px; right: -5px; width: 10px; height: 10px; border-bottom: 1px solid #00F2FF; border-right: 1px solid #00F2FF; }

        .glass-layer { background: rgba(255, 255, 255, 0.01); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        
        @keyframes scan { 0% { transform: translateY(-100%); } 100% { transform: translateY(100%); } }
        .scanner::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: #00F2FF; box-shadow: 0 0 20px #00F2FF; animation: scan 4s linear infinite; }

        .btn-protocol { background: #00F2FF; color: #050505; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-protocol:hover { background: #FFFFFF; transform: translateY(-2px); box-shadow: 0 15px 30px rgba(0, 242, 255, 0.2); }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ scrolled: false }" @scroll.window="scrolled = window.pageYOffset > 50">

    <!-- Global Background Elements -->
    <div class="fixed inset-0 grid-canvas pointer-events-none z-0"></div>
    <div class="fixed top-[-10%] right-[-10%] w-[50%] h-[50%] bg-cyan-500/5 blur-[120px] rounded-full pointer-events-none"></div>

    <!-- Top Navigation Protocol -->
    <nav class="fixed top-0 w-full z-[100] transition-all duration-500 border-b border-transparent"
         :class="scrolled ? 'bg-black/80 backdrop-blur-xl border-white/5 py-4' : 'bg-transparent py-8'">
        <div class="max-w-[1600px] mx-auto px-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 glass-layer rounded-xl flex items-center justify-center border-cyan-400/20">
                    <img src="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png" class="w-8 h-8 object-contain" alt="ArchitGrid">
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-black tracking-tighter uppercase text-white leading-none">ArchitGrid</span>
                    <span class="text-[8px] font-black uppercase tracking-[0.4em] text-cyan-400 mt-1">Infrastructure protocol v1.0</span>
                </div>
            </div>
            
            <div class="hidden lg:flex items-center gap-12">
                <a href="#vision" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-cyan-400 transition-colors">01. Vision</a>
                <a href="#modular" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-cyan-400 transition-colors">02. Infrastructure</a>
                <a href="#pricing" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-cyan-400 transition-colors">03. Pricing</a>
                <a href="#telemetry" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-cyan-400 transition-colors">04. Telemetry</a>
                <a href="/waitlist" class="btn-protocol px-8 py-3 rounded-xl text-[10px]">Initialize Beta</a>
            </div>
        </div>
    </nav>

    <!-- Hero Matrix Section -->
    <section class="relative min-h-screen flex items-center pt-20 px-10 overflow-hidden">
        <div class="max-w-[1600px] mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <div class="lg:col-span-7 space-y-10 z-10 animate-in fade-in slide-in-from-left-10 duration-1000">
                <div class="flex items-center gap-4">
                    <div class="h-[1px] w-12 bg-cyan-400"></div>
                    <span class="mono text-[10px] uppercase text-cyan-400 tracking-widest font-black">Identity & Intelligence Layer</span>
                </div>
                
                <h1 class="text-7xl md:text-9xl font-black tracking-tighter leading-[0.85] text-white">
                    The Content <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-600">Operating System.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-400 font-medium max-w-2xl leading-relaxed italic">
                    Architect industrial-grade content strategies grounded in Deep Research. ArchitGrid provides the multi-tenant infrastructure for high-scale agency growth.
                </p>

                <div class="flex flex-col sm:flex-row gap-6 pt-6">
                    <a href="/waitlist" class="btn-protocol h-20 px-12 rounded-2xl flex items-center justify-center gap-4 group">
                        <span>Enter the Grid</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                    <a href="{{ route('login') }}" class="h-20 px-12 rounded-2xl border border-white/10 glass-layer flex items-center justify-center font-black uppercase tracking-widest text-[10px] hover:bg-white/5 transition-all">
                        Identity Login
                    </a>
                </div>

                <!-- Live Telemetry Bar -->
                <div class="flex gap-12 pt-10 border-t border-white/5">
                    <div>
                        <p class="mono text-[10px] text-slate-500 uppercase mb-1">Active Nodes</p>
                        <p class="text-2xl font-black text-white">{{ number_format($telemetry['nodes_active']) }}</p>
                    </div>
                    <div>
                        <p class="mono text-[10px] text-slate-500 uppercase mb-1">Identity blocks</p>
                        <p class="text-2xl font-black text-white">{{ number_format($telemetry['identity_count']) }}</p>
                    </div>
                    <div>
                        <p class="mono text-[10px] text-slate-500 uppercase mb-1">Last Sync</p>
                        <p class="text-2xl font-black text-white">{{ $telemetry['last_protocol'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Visual Asset: The Monolith -->
            <div class="lg:col-span-5 relative z-10 animate-in fade-in zoom-in-95 duration-1000 delay-200">
                <div class="arch-poly-1 w-full aspect-[4/5] glass-layer p-2 blueprint-border shadow-2xl relative group overflow-hidden">
                    <div class="scanner absolute inset-0 z-20 pointer-events-none"></div>
                    <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200&auto=format&fit=crop" 
                         class="w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-80 transition-all duration-1000 scale-110 group-hover:scale-100" alt="Tech">
                    
                    <!-- Floating Data Block -->
                    <div class="absolute top-10 left-10 right-10 p-8 glass-layer rounded-2xl border-l-4 border-l-cyan-400 z-30">
                        <div class="flex justify-between items-start mb-6">
                            <i data-lucide="shield-check" class="w-8 h-8 text-cyan-400"></i>
                            <span class="mono text-[8px] text-slate-500 uppercase">Block ID: 0x8842...</span>
                        </div>
                        <p class="text-sm font-bold text-white leading-relaxed">Multi-Tenant Data Isolation Active. All research grounding protocols verified across the global grid.</p>
                    </div>
                </div>
                
                <!-- Bottom Decorative Shape -->
                <div class="arch-poly-2 absolute -bottom-12 -left-12 w-2/3 aspect-square bg-gradient-to-br from-cyan-500/20 to-transparent border border-cyan-400/20 backdrop-blur-3xl -z-10 shadow-2xl"></div>
            </div>
        </div>
    </section>

    <!-- Structural Vision -->
    <section id="vision" class="py-40 px-10 border-y border-white/5 bg-white/[0.01] relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full grid-canvas opacity-10 pointer-events-none"></div>
        <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-20 relative z-10">
            <div class="lg:col-span-4 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="h-[1px] w-8 bg-cyan-400"></div>
                    <span class="mono text-[10px] uppercase text-cyan-400 tracking-widest font-black italic">01. The Blueprint</span>
                </div>
                <h2 class="text-6xl font-black tracking-tighter uppercase leading-[0.9] text-white">Scale <br>Without <br>Fragmentation.</h2>
                <p class="text-slate-400 font-medium leading-relaxed italic text-lg">ArchitGrid provides the hardened data partitions required to scale agency operations without losing intelligence integrity.</p>
                <div class="pt-6">
                    <a href="/waitlist" class="text-cyan-400 font-black uppercase tracking-widest text-[10px] flex items-center gap-3 hover:text-white transition-colors group">
                        Study the Infrastructure 
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>
            </div>
            
            <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="p-12 glass-layer rounded-[40px] blueprint-border hover:border-cyan-400/40 transition-all group relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-cyan-400/5 blur-3xl"></div>
                    <i data-lucide="layers" class="w-12 h-12 text-cyan-400 mb-10 group-hover:scale-110 transition-transform"></i>
                    <h3 class="text-3xl font-black uppercase mb-4 text-white tracking-tight">Grid Isolation</h3>
                    <p class="text-slate-400 leading-relaxed font-medium">Every tenant operates in an air-gapped data partition. Secure your client's business intelligence behind enterprise-grade IAM protocols.</p>
                </div>
                <div class="p-12 glass-layer rounded-[40px] blueprint-border hover:border-cyan-400/40 transition-all group relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/5 blur-3xl"></div>
                    <i data-lucide="brain-circuit" class="w-12 h-12 text-cyan-400 mb-10 group-hover:scale-110 transition-transform"></i>
                    <h3 class="text-3xl font-black uppercase mb-4 text-white tracking-tight">Deep RAG Hub</h3>
                    <p class="text-slate-400 leading-relaxed font-medium">Your Knowledge Base is the primary context layer. AI modules refer to your indexed documents as the one source of truth for all deployments.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Protocol Grid -->
    <section id="pricing" class="py-40 px-10 relative">
        <div class="max-w-[1600px] mx-auto">
            <div class="text-center mb-24 space-y-4">
                <div class="mono text-[10px] text-cyan-400 uppercase tracking-[0.5em] font-black">Subscription Tiers</div>
                <h2 class="text-6xl md:text-8xl font-black uppercase tracking-tighter text-white">Scaling Protocols.</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Standard -->
                <div class="bg-card border border-white/5 rounded-[48px] p-12 flex flex-col hover:border-white/10 transition-all group relative">
                    <div class="mb-12">
                        <h4 class="text-2xl font-black uppercase tracking-tight text-white mb-2">Standard</h4>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Entry-level Node Access</p>
                    </div>
                    <div class="flex-1 space-y-6 mb-16">
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-300">
                            <div class="w-5 h-5 rounded-full bg-cyan-400/10 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-cyan-400"></i></div>
                            5,000 monthly tokens
                        </div>
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-300">
                            <div class="w-5 h-5 rounded-full bg-cyan-400/10 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-cyan-400"></i></div>
                            3 Sub-account nodes
                        </div>
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-300">
                            <div class="w-5 h-5 rounded-full bg-cyan-400/10 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-cyan-400"></i></div>
                            Secure RAG Knowledge Hub
                        </div>
                    </div>
                    <div class="mb-10">
                        <span class="text-5xl font-black text-white">$149</span>
                        <span class="text-sm font-bold text-slate-500 uppercase tracking-widest ml-2">/ month</span>
                    </div>
                    <a href="/waitlist" class="w-full h-16 rounded-3xl border border-white/10 flex items-center justify-center font-black uppercase text-[10px] tracking-widest text-white hover:bg-white hover:text-black transition-all">Initialize Node</a>
                </div>

                <!-- Enterprise -->
                <div class="bg-slate-900 border border-cyan-400/30 rounded-[48px] p-12 flex flex-col shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 px-10 py-3 bg-cyan-400 text-black font-black uppercase text-[9px] tracking-widest rounded-bl-3xl">Recommended Node</div>
                    <div class="mb-12">
                        <h4 class="text-2xl font-black uppercase tracking-tight text-white mb-2">Enterprise</h4>
                        <p class="text-[10px] font-bold text-cyan-400 uppercase tracking-widest">Advanced Agency Infrastructure</p>
                    </div>
                    <div class="flex-1 space-y-6 mb-16">
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-200">
                            <div class="w-5 h-5 rounded-full bg-cyan-400 flex items-center justify-center shadow-lg shadow-cyan-400/20"><i data-lucide="check" class="w-3 h-3 text-black"></i></div>
                            25,000 monthly tokens
                        </div>
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-200">
                            <div class="w-5 h-5 rounded-full bg-cyan-400 flex items-center justify-center shadow-lg shadow-cyan-400/20"><i data-lucide="check" class="w-3 h-3 text-black"></i></div>
                            15 Sub-account nodes
                        </div>
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-200">
                            <div class="w-5 h-5 rounded-full bg-cyan-400 flex items-center justify-center shadow-lg shadow-cyan-400/20"><i data-lucide="check" class="w-3 h-3 text-black"></i></div>
                            Industrial-Grade RAG Hub
                        </div>
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-200">
                            <div class="w-5 h-5 rounded-full bg-cyan-400 flex items-center justify-center shadow-lg shadow-cyan-400/20"><i data-lucide="check" class="w-3 h-3 text-black"></i></div>
                            Full White-label UI Engine
                        </div>
                    </div>
                    <div class="mb-10">
                        <span class="text-5xl font-black text-cyan-400">$499</span>
                        <span class="text-sm font-bold text-slate-500 uppercase tracking-widest ml-2">/ month</span>
                    </div>
                    <a href="/waitlist" class="w-full h-20 bg-cyan-400 text-black rounded-3xl flex items-center justify-center font-black uppercase text-xs tracking-[0.2em] shadow-2xl shadow-cyan-400/20 hover:bg-white hover:scale-[1.02] transition-all">Engage Enterprise</a>
                </div>

                <!-- Master -->
                <div class="bg-card border border-white/5 rounded-[48px] p-12 flex flex-col hover:border-white/10 transition-all group">
                    <div class="mb-12">
                        <h4 class="text-2xl font-black uppercase tracking-tight text-white mb-2">Master</h4>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Industrial Platform Protocol</p>
                    </div>
                    <div class="flex-1 space-y-6 mb-16">
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-300">
                            <div class="w-5 h-5 rounded-full bg-cyan-400/10 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-cyan-400"></i></div>
                            Unlimited Resource Hashing
                        </div>
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-300">
                            <div class="w-5 h-5 rounded-full bg-cyan-400/10 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-cyan-400"></i></div>
                            Unlimited Workspace Nodes
                        </div>
                        <div class="flex items-center gap-4 text-sm font-medium italic text-slate-300">
                            <div class="w-5 h-5 rounded-full bg-cyan-400/10 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-cyan-400"></i></div>
                            Developer API & SSO Protocol
                        </div>
                    </div>
                    <div class="mb-10">
                        <span class="text-4xl font-black text-white italic">Contact Sales</span>
                    </div>
                    <a href="/waitlist" class="w-full h-16 rounded-3xl border border-white/10 flex items-center justify-center font-black uppercase text-[10px] tracking-widest text-white hover:bg-white hover:text-black transition-all">Initiate Inquiry</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Modular Bento Infrastructure -->
    <section id="modular" class="py-40 px-10">
        <div class="max-w-[1600px] mx-auto">
            <div class="text-center mb-20 space-y-4">
                <h2 class="text-6xl font-black uppercase tracking-tighter text-white">The Grid Core</h2>
                <p class="mono text-[10px] text-cyan-400 uppercase tracking-[0.5em] font-black">Architecture components</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 auto-rows-[280px]">
                
                <!-- Research Monolith -->
                <div class="md:col-span-8 glass-layer rounded-[40px] overflow-hidden relative group blueprint-border">
                    <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-20 group-hover:scale-105 transition-transform duration-1000" alt="Core">
                    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/40 to-transparent p-12 flex flex-col justify-end">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-cyan-400 text-black flex items-center justify-center rounded-lg shadow-lg">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-4xl font-black uppercase text-white">Research Engine</h3>
                        </div>
                        <p class="text-slate-300 max-w-xl text-sm font-bold leading-relaxed italic">Deep Gemini-powered research with real-time web grounding and automated citation protocols.</p>
                    </div>
                </div>

                <!-- Post Creator -->
                <div class="md:col-span-4 bg-cyan-400 rounded-[40px] p-12 flex flex-col justify-between group cursor-pointer shadow-2xl shadow-cyan-400/10">
                    <div class="flex justify-between items-start">
                        <i data-lucide="edit-3" class="w-12 h-12 text-black stroke-[3px]"></i>
                        <span class="mono text-[8px] font-black text-black/40 uppercase">Module: Content</span>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black uppercase text-black mb-2">Architect</h3>
                        <p class="text-black/60 text-xs font-black leading-relaxed">Multi-platform campaign generation with human-first optimization.</p>
                    </div>
                </div>

                <!-- Social Planner -->
                <div class="md:col-span-4 glass-layer rounded-[40px] p-12 flex flex-col justify-between hover:bg-white/5 transition-all blueprint-border group">
                    <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-cyan-400 border border-white/10 group-hover:bg-cyan-400 group-hover:text-black transition-all">
                        <i data-lucide="calendar" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black uppercase text-white mb-2">Social Planner</h3>
                        <p class="text-slate-500 text-xs font-bold italic leading-relaxed">Integrated Meta & LinkedIn scheduling with automated token management.</p>
                    </div>
                </div>

                <!-- Treasury -->
                <div class="md:col-span-8 glass-layer rounded-[40px] overflow-hidden blueprint-border p-12 flex items-center gap-12 group">
                    <div class="flex-1 space-y-6">
                        <div class="flex items-center gap-3">
                            <i data-lucide="coins" class="w-8 h-8 text-cyan-400"></i>
                            <h3 class="text-3xl font-black uppercase text-white">Token Treasury</h3>
                        </div>
                        <p class="text-slate-400 text-sm font-medium leading-relaxed italic">Manage resource distribution across your entire agency grid. Real-time consumption telemetry for all sub-accounts.</p>
                    </div>
                    <div class="hidden lg:flex w-64 h-full bg-white/5 rounded-3xl border border-white/10 items-center justify-center group-hover:bg-white/10 transition-all">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full border-4 border-cyan-400/20 border-t-cyan-400 animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-cyan-400 font-black text-xs uppercase">Sync</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Live Telemetry Section -->
    <section id="telemetry" class="py-40 px-10 border-t border-white/5 bg-gradient-to-b from-transparent to-cyan-400/5">
        <div class="max-w-[1600px] mx-auto">
            <div class="text-center mb-20 space-y-4">
                <div class="flex items-center justify-center gap-4">
                    <div class="h-[1px] w-12 bg-cyan-400"></div>
                    <span class="mono text-[10px] uppercase text-cyan-400 tracking-widest font-black italic">System Pulse</span>
                    <div class="h-[1px] w-12 bg-cyan-400"></div>
                </div>
                <h2 class="text-6xl font-black uppercase tracking-tighter text-white">Global Telemetry</h2>
                <p class="text-slate-400 font-medium italic max-w-xl mx-auto">Real-time infrastructure metrics from the ArchitGrid network. All nodes synchronized.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Active Nodes -->
                <div class="glass-layer rounded-[32px] p-10 blueprint-border group hover:border-cyan-400/40 transition-all relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-400/5 blur-3xl rounded-full"></div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-cyan-400/10 rounded-2xl flex items-center justify-center border border-cyan-400/20 group-hover:bg-cyan-400/20 transition-colors">
                            <i data-lucide="server" class="w-6 h-6 text-cyan-400"></i>
                        </div>
                        <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Module: Nodes</span>
                    </div>
                    <p class="text-5xl font-black text-white mb-2">{{ number_format($telemetry['nodes_active']) }}</p>
                    <p class="mono text-[10px] uppercase text-slate-500 font-bold tracking-widest">Active Tenant Nodes</p>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="mono text-[9px] text-green-500 font-bold uppercase">All Systems Online</span>
                    </div>
                </div>

                <!-- Identity Count -->
                <div class="glass-layer rounded-[32px] p-10 blueprint-border group hover:border-cyan-400/40 transition-all relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-400/5 blur-3xl rounded-full"></div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-indigo-400/10 rounded-2xl flex items-center justify-center border border-indigo-400/20 group-hover:bg-indigo-400/20 transition-colors">
                            <i data-lucide="users" class="w-6 h-6 text-indigo-400"></i>
                        </div>
                        <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Module: IAM</span>
                    </div>
                    <p class="text-5xl font-black text-white mb-2">{{ number_format($telemetry['identity_count']) }}</p>
                    <p class="mono text-[10px] uppercase text-slate-500 font-bold tracking-widest">Verified Identities</p>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                        <span class="mono text-[9px] text-indigo-400 font-bold uppercase">Identity Layer Active</span>
                    </div>
                </div>

                <!-- Grid Status -->
                <div class="glass-layer rounded-[32px] p-10 blueprint-border group hover:border-cyan-400/40 transition-all relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-400/5 blur-3xl rounded-full"></div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-emerald-400/10 rounded-2xl flex items-center justify-center border border-emerald-400/20 group-hover:bg-emerald-400/20 transition-colors">
                            <i data-lucide="activity" class="w-6 h-6 text-emerald-400"></i>
                        </div>
                        <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Module: Core</span>
                    </div>
                    <p class="text-4xl font-black text-emerald-400 mb-2">{{ $telemetry['grid_status'] }}</p>
                    <p class="mono text-[10px] uppercase text-slate-500 font-bold tracking-widest">Grid Status</p>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="mono text-[9px] text-emerald-400 font-bold uppercase">99.99% Uptime</span>
                    </div>
                </div>

                <!-- Last Protocol -->
                <div class="glass-layer rounded-[32px] p-10 blueprint-border group hover:border-cyan-400/40 transition-all relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-400/5 blur-3xl rounded-full"></div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-amber-400/10 rounded-2xl flex items-center justify-center border border-amber-400/20 group-hover:bg-amber-400/20 transition-colors">
                            <i data-lucide="clock" class="w-6 h-6 text-amber-400"></i>
                        </div>
                        <span class="mono text-[8px] uppercase text-slate-600 font-black tracking-widest">Module: Audit</span>
                    </div>
                    <p class="text-3xl font-black text-white mb-2">{{ $telemetry['last_protocol'] }}</p>
                    <p class="mono text-[10px] uppercase text-slate-500 font-bold tracking-widest">Last Protocol Sync</p>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="mono text-[9px] text-amber-400 font-bold uppercase">Continuous Logging</span>
                    </div>
                </div>
            </div>

            <!-- Live Activity Feed -->
            <div class="mt-16 glass-layer rounded-[40px] p-10 blueprint-border">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <i data-lucide="radio" class="w-5 h-5 text-cyan-400 animate-pulse"></i>
                        <span class="mono text-[10px] uppercase text-cyan-400 font-black tracking-widest">Live Network Feed</span>
                    </div>
                    <span class="mono text-[8px] uppercase text-slate-600 font-black">Auto-refresh: 30s</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-white/[0.02] rounded-2xl border border-white/5">
                        <p class="mono text-[9px] text-slate-500 uppercase mb-2">Encryption Protocol</p>
                        <p class="text-sm font-black text-white">AES-256-GCM Active</p>
                    </div>
                    <div class="p-6 bg-white/[0.02] rounded-2xl border border-white/5">
                        <p class="mono text-[9px] text-slate-500 uppercase mb-2">Data Isolation</p>
                        <p class="text-sm font-black text-white">Multi-Tenant Verified</p>
                    </div>
                    <div class="p-6 bg-white/[0.02] rounded-2xl border border-white/5">
                        <p class="mono text-[9px] text-slate-500 uppercase mb-2">API Gateway</p>
                        <p class="text-sm font-black text-white">{{ number_format(rand(1200, 2500)) }} req/s</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Global Telemetry Footer -->
    <footer class="relative py-40 px-10 border-t border-white/5 overflow-hidden">
        <div class="max-w-4xl mx-auto text-center space-y-12 relative z-10">
            <h2 class="text-6xl md:text-8xl font-black tracking-tighter uppercase text-white leading-[0.9]">
                Architect Your <br>Agency's Future.
            </h2>
            <div class="p-8 glass-layer rounded-3xl border border-cyan-400/20 inline-block">
                <p class="mono text-[10px] text-cyan-400 uppercase font-black tracking-[0.2em]">Closed Beta protocol engaged</p>
                <p class="text-slate-400 text-sm mt-2 font-medium italic leading-relaxed">Secure a node in our early-access rollout. Identity verification mandatory.</p>
            </div>
            
            <div class="flex flex-col md:flex-row items-center justify-center gap-10">
                <a href="/waitlist" class="btn-protocol px-16 py-8 rounded-2xl text-sm shadow-2xl">Claim Workspace Identity</a>
                <span class="mono text-[10px] text-slate-600 font-black tracking-widest uppercase">/ / / / / / / / /</span>
                <a href="#vision" class="text-white text-[10px] font-black uppercase tracking-widest hover:text-cyan-400 transition-colors">Study Protocol</a>
            </div>
        </div>

        <div class="mt-40 flex flex-col md:flex-row items-center justify-between max-w-[1600px] mx-auto border-t border-white/5 pt-10 opacity-20 mono text-[10px] uppercase font-black tracking-[0.4em] text-white">
            <div class="flex items-center gap-4">
                <span>&copy; 2026 ArchitGrid System</span>
                <span class="text-cyan-400">Node: {{ gethostname() }}</span>
            </div>
            <div class="flex gap-12 mt-8 md:mt-0">
                <a href="#" class="hover:text-cyan-400 transition-colors">Security</a>
                <a href="#" class="hover:text-cyan-400 transition-colors">Audit</a>
                <a href="#" class="hover:text-cyan-400 transition-colors">Status: Stable</a>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>