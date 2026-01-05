<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArchitGrid | Beta Protocol Initiation</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:900|inter:400,500,700,800|jetbrains-mono:500" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505; color: #E2E8F0; overflow: hidden; }
        h1, h2 { font-family: 'Montserrat', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        
        .grid-canvas {
            background-image: 
                linear-gradient(to right, rgba(0, 242, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 242, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(ellipse at center, black, transparent 90%);
        }

        .blueprint-border { border: 1px solid rgba(0, 242, 255, 0.1); position: relative; }
        .blueprint-border::before { content: ''; position: absolute; top: -2px; left: -2px; width: 8px; height: 8px; border-top: 2px solid #00F2FF; border-left: 2px solid #00F2FF; }
        .blueprint-border::after { content: ''; position: absolute; bottom: -2px; right: -2px; width: 8px; height: 8px; border-bottom: 2px solid #00F2FF; border-right: 2px solid #00F2FF; }

        .glass-layer { background: rgba(255, 255, 255, 0.01); backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.05); }
        
        input:focus { box-shadow: 0 0 20px rgba(0, 242, 255, 0.1); }
        
        @keyframes pulse-slow { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.1; } }
        .pulse-bg { animation: pulse-slow 4s infinite; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center p-6">

    <!-- Background Matrix -->
    <div class="fixed inset-0 grid-canvas pointer-events-none"></div>
    <div class="fixed inset-0 bg-gradient-to-tr from-cyan-500/5 via-transparent to-blue-500/5 pointer-events-none"></div>

    <!-- Navigation / Abort -->
    <nav class="fixed top-10 w-full px-10 flex justify-between items-center z-50">
        <a href="/" class="flex items-center gap-3 group transition-all">
            <div class="w-8 h-8 glass-layer rounded-lg flex items-center justify-center border-white/10 group-hover:border-cyan-400/50 transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500 group-hover:text-cyan-400 transition-colors"></i>
            </div>
            <span class="mono text-[9px] font-black uppercase tracking-[0.3em] text-slate-500 group-hover:text-white transition-colors">Abort Protocol</span>
        </a>
        <div class="flex flex-col items-end opacity-20">
            <span class="mono text-[8px] font-black uppercase tracking-widest">Node Identification</span>
            <span class="mono text-[8px] uppercase tracking-tighter">{{ gethostname() }}</span>
        </div>
    </nav>

    <div class="w-full max-w-2xl relative z-10">
        
        <!-- Header DNA -->
        <div class="flex flex-col items-center mb-12 animate-in fade-in slide-in-from-top-4 duration-1000">
            <div class="w-20 h-20 glass-layer rounded-2xl flex items-center justify-center border-cyan-400/20 mb-6 relative group overflow-hidden">
                <div class="absolute inset-0 bg-cyan-400/5 pulse-bg"></div>
                <img src="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png" class="w-12 h-12 object-contain relative z-10" alt="ArchitGrid">
            </div>
            <h1 class="text-4xl font-black tracking-tighter uppercase text-white text-center">Beta Protocol Initiation</h1>
            <p class="mono text-[10px] text-cyan-400 uppercase tracking-[0.4em] mt-2 font-black">Identity Provisioning Queue</p>
        </div>

        @if(session('success'))
            <!-- Success State: Access Granted -->
            <div class="glass-layer rounded-[40px] p-12 text-center space-y-8 animate-in zoom-in-95 duration-500 shadow-2xl border-cyan-400/20">
                <div class="w-20 h-20 rounded-full bg-cyan-400/10 flex items-center justify-center mx-auto border border-cyan-400/30">
                    <i data-lucide="shield-check" class="w-10 h-10 text-cyan-400"></i>
                </div>
                <div class="space-y-2">
                    <h2 class="text-3xl font-black uppercase tracking-tight text-white">Queue Initialized</h2>
                    <p class="text-slate-400 font-medium italic leading-relaxed">
                        Your agency identity has been successfully hashed and added to the deployment queue.
                    </p>
                </div>
                <div class="bg-black/40 rounded-2xl p-6 border border-white/5 space-y-4">
                    <p class="mono text-[9px] text-slate-500 uppercase tracking-widest">Protocol Status</p>
                    <div class="flex justify-between items-center px-4">
                        <span class="text-xs font-bold text-white uppercase">Identity State:</span>
                        <span class="text-xs font-black text-cyan-400 uppercase tracking-widest">Awaiting Provisioning</span>
                    </div>
                    <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-cyan-400 h-full w-1/3 animate-pulse"></div>
                    </div>
                </div>
                <div class="pt-4">
                    <a href="/" class="mono text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 hover:text-white transition-colors">Return to Network</a>
                </div>
            </div>
        @else
            <!-- Initiation Form -->
            <div class="glass-layer rounded-[40px] p-1 pb-1 shadow-2xl relative overflow-hidden">
                
                <form action="{{ route('waitlist.join') }}" method="POST" class="p-12 space-y-10 relative z-10" x-data="{ state: 'idle' }">
                    @csrf
                    
                    <div class="space-y-8">
                        <!-- Email Block -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center px-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic">Target Identity (Email)</label>
                                <span class="mono text-[8px] text-cyan-400/50 uppercase">Mandatory</span>
                            </div>
                            <div class="relative">
                                <input type="email" name="email" required placeholder="architect@youragency.com"
                                       @focus="state = 'scanning'" @blur="state = 'idle'"
                                       class="w-full h-16 bg-white/[0.03] border border-white/10 rounded-2xl px-6 mono text-sm font-bold text-white focus:ring-2 focus:ring-cyan-400/20 focus:border-cyan-400/50 outline-none transition-all placeholder:text-slate-700">
                                <i data-lucide="fingerprint" class="absolute right-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-700"></i>
                            </div>
                            @error('email') <p class="mono text-[9px] text-red-500 font-bold px-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Identity Name</label>
                                <input type="text" name="name" placeholder="John Doe"
                                       class="w-full h-16 bg-white/[0.03] border border-white/10 rounded-2xl px-6 mono text-sm font-bold text-white focus:ring-2 focus:ring-cyan-400/20 outline-none transition-all placeholder:text-slate-700">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Agency Brand</label>
                                <input type="text" name="agency_name" placeholder="Acme Digital"
                                       class="w-full h-16 bg-white/[0.03] border border-white/10 rounded-2xl px-6 mono text-sm font-bold text-white focus:ring-2 focus:ring-cyan-400/20 outline-none transition-all placeholder:text-slate-700">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 relative">
                        <!-- Decorative Specs -->
                        <div class="absolute -top-4 left-0 w-full flex justify-between px-2 opacity-20">
                            <span class="mono text-[7px] text-slate-500 uppercase">SYS_LOAD: 0.042s</span>
                            <span class="mono text-[7px] text-slate-500 uppercase">ENCR: AES-256</span>
                        </div>
                        
                        <button type="submit" class="w-full h-20 bg-cyan-400 text-black rounded-3xl font-black uppercase tracking-[0.4em] text-xs shadow-2xl shadow-cyan-400/20 hover:bg-white hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 group">
                            <i data-lucide="zap" class="w-5 h-5 fill-current group-hover:animate-pulse"></i>
                            <span>Initiate Protocol</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest leading-relaxed">
                            BY INITIATING, YOU AUTHORIZE IDENTITY DATA <br> RETENTION FOR PROVISIONING PURPOSES ONLY.
                        </p>
                    </div>
                </form>

                <!-- Bottom Blueprint Accent -->
                <div class="h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent opacity-20"></div>
            </div>
        @endif

        <!-- Global Status Bar -->
        <div class="mt-16 flex justify-between items-center opacity-30 px-4">
            <div class="flex gap-6">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    <span class="mono text-[8px] uppercase tracking-widest text-white">Auth Node Active</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                    <span class="mono text-[8px] uppercase tracking-widest text-white">Grid Sync: 100%</span>
                </div>
            </div>
            <p class="mono text-[8px] font-black uppercase tracking-[0.4em] text-white">ArchitGrid v1.0.4</p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>