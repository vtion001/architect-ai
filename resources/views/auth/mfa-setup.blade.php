@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-card border border-border rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-1 text-center text-primary">Fortify Your Account</h2>
            <p class="text-sm text-muted-foreground mb-8 text-center">Scan the QR code below with your authenticator app to enable Multi-Factor Authentication.</p>

            <div class="flex flex-col items-center gap-8 mb-8 p-6 bg-muted/10 rounded-2xl border border-border/50">
                <div class="bg-white p-4 rounded-xl shadow-inner border border-border">
                    {!! $qrCodeUrl !!}
                </div>
                <div class="text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-1">Backup Strategy</p>
                    <p class="text-xs font-medium text-foreground">Save this code in a secure place.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('mfa.enable') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 text-center block w-full">Enter 6-Digit Code to Confirm</label>
                    <input type="text" name="code" placeholder="000000" maxlength="6" required autofocus
                           class="w-full h-14 text-center text-2xl font-black tracking-[0.5em] bg-muted/20 border border-border rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit"
                            class="w-full h-12 bg-primary text-primary-foreground rounded-xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Activate & Continue
                    </button>
                    <a href="{{ route('dashboard') }}" class="text-center text-[10px] font-bold text-muted-foreground uppercase tracking-widest hover:text-foreground transition-colors">Setup Later</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
