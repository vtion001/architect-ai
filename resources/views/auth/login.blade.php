@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md" x-data="{
    slug: '{{ $slug ?? '' }}',
    email: '',
    password: '',
    isLoading: false,
    errorMessage: '',
    
    login() {
        this.isLoading = true;
        this.errorMessage = '';
        
        fetch('/auth/login', {
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
                this.errorMessage = data.message || 'Login failed';
                this.isLoading = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.errorMessage = 'An unexpected error occurred.';
            this.isLoading = false;
        });
    }
}">
    <div class="bg-card border border-border rounded-3xl shadow-2xl overflow-hidden shadow-primary/5">
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-1">Sign In</h2>
            <p class="text-sm text-muted-foreground mb-8">Access your workspace using your credentials.</p>

            <template x-if="errorMessage">
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm font-medium flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <form @submit.prevent="login" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Workspace Slug</label>
                    <div class="relative">
                        <i data-lucide="hash" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                        <input x-model="slug" type="text" placeholder="my-agency" required
                               class="w-full h-12 pl-11 bg-muted/20 border border-border rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                               :readonly="'{{ $slug ?? '' }}' !== ''">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                        <input x-model="email" type="email" placeholder="name@company.com" required
                               class="w-full h-12 pl-11 bg-muted/20 border border-border rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Password</label>
                        <a href="#" class="text-[10px] font-bold text-primary hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                        <input x-model="password" type="password" placeholder="••••••••••••" required
                               class="w-full h-12 pl-11 bg-muted/20 border border-border rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <button type="submit" :disabled="isLoading"
                        class="w-full h-12 bg-primary text-primary-foreground rounded-xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <template x-if="isLoading">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                    <span x-text="isLoading ? 'Verifying...' : 'Sign In to Dashboard'"></span>
                </button>
            </form>
        </div>
        
        <div class="bg-muted/30 p-6 border-t border-border text-center">
            <p class="text-sm text-muted-foreground font-medium">
                New to Architect? <a href="/auth/register" class="text-primary font-bold hover:underline">Create an Agency</a>
            </p>
        </div>
    </div>
</div>

<script>
    // Hidden Developer Entry Point: Ctrl+Shift+D
    window.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.shiftKey && e.code === 'KeyD') {
            window.location.href = '{{ route('admin.dashboard') }}';
        }
    });
</script>
@endsection
