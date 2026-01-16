{{--
    Login Page
    
    Workspace authentication with multi-tenant support.
    
    Required variables:
    - $slug: Optional pre-filled workspace slug
--}}

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
    <div class="bg-card border border-border rounded-[40px] shadow-2xl overflow-hidden relative">
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/5 rounded-full blur-3xl"></div>
        
        <div class="p-10 md:p-12 space-y-10 relative z-10">
            <div class="text-center">
                <h2 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Welcome Back</h2>
                <p class="text-sm text-muted-foreground font-medium italic">Enter your credentials to access the grid.</p>
            </div>

            @include('auth.partials.messages')

            @include('auth.partials.login-form')
        </div>
        
        <div class="bg-muted/30 p-8 text-center border-t border-border">
            <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">
                Need a workspace? <a href="{{ url('/waitlist') }}" class="text-primary hover:underline ml-1">Join the Waitlist</a>
            </p>
        </div>
    </div>
</div>
@endsection