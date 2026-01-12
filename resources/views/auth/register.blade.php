@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md animate-in fade-in zoom-in-95 duration-500" x-data="{
    companyName: '',
    slug: '',
    email: '',
    password: '',
    isLoading: false,
    errorMessage: '',
    
    register() {
        this.isLoading = true;
        this.errorMessage = '';
        
        fetch('{{ url('/auth/register-agency') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                company_name: this.companyName,
                slug: this.slug,
                email: this.email,
                password: this.password
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                window.location.href = data.login_url;
            } else {
                this.errorMessage = data.message || 'Registration failed';
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
                <h2 class="text-2xl font-bold text-white mb-1">Create Workspace</h2>
                <p class="text-sm text-slate-400">Launch your agency on ArchitGrid.</p>
            </div>

            <template x-if="errorMessage">
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-medium flex items-center gap-3 animate-in slide-in-from-top-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <form @submit.prevent="register" class="space-y-6">
                <div class="space-y-5">
                    <!-- Agency Name -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Agency Name</label>
                        <div class="relative">
                            <i data-lucide="briefcase" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="companyName" type="text" placeholder="Acme Digital" required
                                   @input="slug = companyName.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')"
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Workspace ID (Slug)</label>
                        <div class="relative">
                            <i data-lucide="link" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="slug" type="text" placeholder="acme-digital" required
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-xs text-cyan-400 font-mono focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Owner Email</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="email" type="email" placeholder="owner@acme.com" required
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 px-1">Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input x-model="password" type="password" placeholder="Min. 8 characters" required
                                   class="w-full h-12 bg-white/5 border border-white/10 rounded-xl pl-11 text-sm text-white focus:border-cyan-500/50 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <button type="submit" :disabled="isLoading"
                        class="w-full h-12 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <template x-if="isLoading">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                    <span x-text="isLoading ? 'Creating...' : 'Create Workspace'"></span>
                </button>
            </form>
        </div>
        
        <div class="bg-white/5 p-6 text-center border-t border-white/10">
            <p class="text-xs text-slate-400">
                Already have a workspace? <a href="/auth/login" class="text-cyan-400 font-bold hover:underline ml-1">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection