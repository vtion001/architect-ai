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
    <script>
        setTimeout(() => window.location.reload(), 3000);
    </script>
</div>
