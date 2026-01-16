{{-- Landing Page Video Modal --}}
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
