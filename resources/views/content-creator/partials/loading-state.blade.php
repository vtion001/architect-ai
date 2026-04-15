{{-- Loading State Partial --}}
<div class="min-h-[80vh] flex flex-col items-center justify-center p-10">
    <div class="relative w-24 h-24 mb-8">
        <div class="absolute inset-0 border-t-2 border-primary rounded-full animate-spin"></div>
        <div class="absolute inset-2 border-r-2 border-purple-500 rounded-full animate-spin" style="animation-duration: 1.5s"></div>
        <div class="absolute inset-4 border-b-2 border-cyan-500 rounded-full animate-spin" style="animation-direction: reverse"></div>
    </div>
    <h2 class="text-2xl font-black uppercase tracking-tight text-slate-800">Architecting Content</h2>
    <p class="text-sm font-mono text-slate-500 mt-2 uppercase tracking-widest animate-pulse">
        {{ $content->type === 'video' ? 'Rendering Video Assets...' : 'Synthesizing Text & Context...' }}
    </p>
    <p class="text-xs font-mono text-slate-400 mt-4" id="lsStatus">Checking status...</p>
    <script>
        (function() {
            var dots = 0;
            var el = document.getElementById('lsStatus');
            setInterval(function() {
                dots = (dots + 1) % 4;
                el.textContent = 'Checking status' + '.'.repeat(dots + 1);
            }, 1000);
            setTimeout(function() { window.location.reload(); }, 5000);
        })();
    </script>
</div>
