{{-- Landing Page Hero Section --}}
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
