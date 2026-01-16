{{-- Landing Page Workflow Section --}}
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
