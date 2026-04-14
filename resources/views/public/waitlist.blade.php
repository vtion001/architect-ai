<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArchitGrid | Join the Waitlist</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0B0F19; color: #E2E8F0; }
        h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        
        input:focus { box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2); border-color: rgba(6, 182, 212, 0.5); }
        
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Background Gradients -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-cyan-500/10 blur-[120px] rounded-full pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-600/10 blur-[100px] rounded-full pointer-events-none -z-10"></div>

    <!-- Navigation -->
    <nav class="absolute top-8 left-8 z-50">
        <a href="/" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors group">
            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-white/10 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </div>
            <span class="text-sm font-medium">Back to Home</span>
        </a>
    </nav>

    <div class="w-full max-w-lg relative z-10">
        
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-cyan-400 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-cyan-500/20 mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Join the Waitlist</h1>
            <p class="text-slate-400">Secure your spot for early access to the ArchitGrid beta.</p>
        </div>

        @if(session('success'))
            <!-- Success State -->
            <div class="glass-card rounded-3xl p-10 text-center animate-in zoom-in-95 duration-500">
                <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center text-green-400 mx-auto mb-6">
                    <i data-lucide="check" class="w-8 h-8"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-4">You're on the list!</h2>
                <p class="text-slate-400 mb-8">
                    We've reserved your spot. Keep an eye on your inbox—we'll notify you as soon as a workspace opens up.
                </p>
                <a href="/" class="inline-flex items-center justify-center h-12 px-8 rounded-xl bg-white/10 text-white font-medium hover:bg-white/20 transition-all">
                    Return to Homepage
                </a>
            </div>
        @else
            <!-- Form -->
            <div class="glass-card rounded-3xl p-8 md:p-10 shadow-2xl">
                <form action="{{ route('waitlist.join') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-300">Work Email <span class="text-cyan-400">*</span></label>
                        <div class="relative">
                            <input type="email" name="email" required placeholder="name@company.com"
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 pl-11 text-white placeholder:text-slate-600 outline-none transition-all">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                        </div>
                        @error('email') <p class="text-xs text-red-400 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-300">Full Name</label>
                            <input type="text" name="name" placeholder="Sarah Smith"
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white placeholder:text-slate-600 outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-300">Agency Name</label>
                            <input type="text" name="agency_name" placeholder="Smith Digital"
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl px-4 text-white placeholder:text-slate-600 outline-none transition-all">
                        </div>
                    </div>

                    <button type="submit" class="w-full h-14 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl font-bold shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <span>Join Waitlist</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>

                    <p class="text-xs text-center text-slate-500">
                        No spam. Unsubscribe at any time.
                    </p>
                </form>
            </div>
        @endif

        <div class="mt-8 text-center">
            <p class="text-xs text-slate-600">
                &copy; 2026 ArchitGrid Inc.
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
