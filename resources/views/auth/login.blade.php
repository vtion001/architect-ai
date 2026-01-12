@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md animate-in fade-in zoom-in-95 duration-500" x-data="{
    slug: '{{ $slug ?? '' }}',
    email: '',
    password: '',
    isLoading: false,
    errorMessage: '',
    
    login() {
        this.isLoading = true;
        this.errorMessage = '';
        
        fetch('{{ url('/auth/login') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                slug: this.slug,
                email: this.email,
                password: this.password
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                window.location.href = '/dashboard';
            } else {
                this.errorMessage = data.message || 'Access Denied';
                this.isLoading = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.errorMessage = 'Connection failure.';
            this.isLoading = false;
        });
    }
}">
    <div class="glass-card rounded-[32px] shadow-2xl overflow-hidden border border-white/10">
        <div class="p-8 md:p-10 space-y-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-white mb-1">Welcome Back</h2>
                <p class="text-sm text-slate-400">Sign in to manage your workspaces.</p>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-xs font-medium flex items-center gap-3 animate-in fade-in">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <template x-if="errorMessage">
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-medium flex items-center gap-3 animate-in slide-in-from-top-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <form @submit.prevent="login" class="space-y-6">
                <div class="space-y-5">
                    <!-- Slug -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Workspace ID</label>
                        <div class="relative">
                            <i data-lucide="hash" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="slug" type="text" placeholder="your-agency-slug" required
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Email Address</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="email" type="email" placeholder="name@agency.com" required
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-1">
                            <label class="text-xs font-semibold text-slate-400">Password</label>
                            <a href="#" class="text-xs text-cyan-400 hover:text-cyan-300 transition-colors">Forgot?</a>
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="password" type="password" placeholder="••••••••••••" required
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <button type="submit" :disabled="isLoading"
                        class="w-full h-12 bg-white text-slate-900 rounded-xl font-bold text-sm shadow-lg hover:bg-cyan-50 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 disabled:opacity-50">
                    <template x-if="isLoading">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                    <span x-text="isLoading ? 'Signing in...' : 'Sign In'"></span>
                </button>
            </form>
        </div>
        
        <div class="bg-white/5 p-6 text-center border-t border-white/10">
            <p class="text-xs text-slate-400">
                Don't have a workspace? <a href="{{ url('/waitlist') }}" class="text-cyan-400 font-bold hover:underline">Join the Waitlist</a>
            </p>
        </div>
    </div>
</div>
@endsection