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
            this.errorMessage = 'Identity verification failed.';
            this.isLoading = false;
        });
    }
}">
    <div class="bg-card border border-border rounded-3xl shadow-2xl relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>

        <div class="p-10 space-y-8 relative z-10">
            <div class="text-center">
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-1">Identity Access</h2>
                <p class="text-sm text-muted-foreground italic font-medium">Verify your credentials to enter the grid.</p>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-50 border border-green-100 text-green-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <template x-if="errorMessage">
                <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-in slide-in-from-top-2">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <form @submit.prevent="login" class="space-y-6">
                <div class="space-y-5">
                    <!-- Slug (Workspace Context) -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Workspace Node (Slug)</label>
                        <div class="relative">
                            <i data-lucide="grid" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="slug" type="text" placeholder="your-agency-slug" required
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Authorized Identity (Email)</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="email" type="email" placeholder="name@agency.com" required
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Passphrase -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 flex items-center justify-between">
                            Access Key
                            <a href="#" class="text-primary hover:underline lowercase tracking-normal">forgot passphrase?</a>
                        </label>
                        <div class="relative">
                            <i data-lucide="key" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="password" type="password" placeholder="••••••••••••" required
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" :disabled="isLoading"
                            class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-3 disabled:opacity-50">
                        <template x-if="isLoading">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isLoading ? 'AUTHENTICATING...' : 'ESTABLISH CONNECTION'"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-muted/30 p-6 border-t border-border text-center">
            <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest leading-relaxed">
                Need a new node? <br>
                <a href="{{ url('/waitlist') }}" class="text-primary hover:underline">Apply for Beta Protocol</a>
            </p>
        </div>
    </div>
</div>
@endsection