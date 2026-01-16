{{-- Login - Session Messages --}}
@if(session('success'))
    <div class="p-4 rounded-2xl bg-primary/10 border border-primary/20 text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-in fade-in">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<template x-if="errorMessage">
    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-in slide-in-from-top-2">
        <i data-lucide="alert-circle" class="w-4 h-4"></i>
        <span x-text="errorMessage"></span>
    </div>
</template>
