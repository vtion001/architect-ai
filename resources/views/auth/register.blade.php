@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md" x-data="{
    companyName: '',
    slug: '',
    email: '',
    password: '',
    isLoading: false,
    errorMessage: '',
    
    register() {
        this.isLoading = true;
        this.errorMessage = '';
        
        fetch('/auth/register-agency', {
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
                alert('Registration successful! Redirecting to login...');
                window.location.href = '/auth/login/' + this.slug;
            } else {
                this.errorMessage = data.message || 'Registration failed';
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
            <h2 class="text-2xl font-bold mb-1">Launch Your Agency</h2>
            <p class="text-sm text-muted-foreground mb-8">Establish your tenant root and start architecting.</p>

            <template x-if="errorMessage">
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm font-medium flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <form @submit.prevent="register" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Agency Name</label>
                    <div class="relative">
                        <i data-lucide="building" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                        <input x-model="companyName" type="text" placeholder="Acme Marketing Group" required
                               @input="slug = companyName.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')"
                               class="w-full h-12 pl-11 bg-muted/20 border border-border rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Workspace Slug (URL)</label>
                    <div class="relative">
                        <i data-lucide="globe" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                        <input x-model="slug" type="text" placeholder="acme-marketing" required
                               class="w-full h-12 pl-11 bg-muted/20 border border-border rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    <p class="text-[9px] text-muted-foreground px-1 italic">This will be your unique identifier: architect-ai.io/<span class="font-bold text-primary" x-text="slug || 'your-slug'"></span></p>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Owner Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                        <input x-model="email" type="email" placeholder="owner@acme.com" required
                               class="w-full h-12 pl-11 bg-muted/20 border border-border rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Security Passphrase</label>
                    <div class="relative">
                        <i data-lucide="shield-check" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                        <input x-model="password" type="password" placeholder="Min. 12 characters" required
                               class="w-full h-12 pl-11 bg-muted/20 border border-border rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <button type="submit" :disabled="isLoading"
                        class="w-full h-12 bg-primary text-primary-foreground rounded-xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <template x-if="isLoading">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                    <span x-text="isLoading ? 'Architecting...' : 'Provision Agency Workspace'"></span>
                </button>
            </form>
        </div>
        
        <div class="bg-muted/30 p-6 border-t border-border text-center">
            <p class="text-sm text-muted-foreground font-medium">
                Already have a workspace? <a href="/auth/login" class="text-primary font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection
