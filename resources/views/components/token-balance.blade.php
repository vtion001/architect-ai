{{--
    Token Balance Component
    
    Displays the current tenant's token balance with optional styling variants.
    
    Usage:
        <x-token-balance />
        <x-token-balance variant="compact" />
        <x-token-balance variant="detailed" :balance="$customBalance" />
    
    Props:
        - variant: 'default' | 'compact' | 'detailed'
        - balance: Optional custom balance (defaults to current tenant)
--}}

@props([
    'variant' => 'default',
    'balance' => null,
])

@php
    $tokenBalance = $balance ?? app(\App\Services\TokenService::class)->getBalance(auth()->user()->tenant);
@endphp

@if($variant === 'compact')
    {{-- Compact: Just number with icon --}}
    <div {{ $attributes->merge(['class' => 'flex items-center gap-1.5']) }}>
        <i data-lucide="coins" class="w-3.5 h-3.5 text-primary"></i>
        <span class="text-sm font-bold">{{ number_format($tokenBalance) }}</span>
    </div>

@elseif($variant === 'detailed')
    {{-- Detailed: Full card with label --}}
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-border bg-card p-4']) }}>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Token Balance</p>
                <p class="text-2xl font-black text-foreground">{{ number_format($tokenBalance) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                <i data-lucide="coins" class="w-6 h-6 text-primary"></i>
            </div>
        </div>
        @if($tokenBalance < 100)
            <p class="mt-2 text-xs text-amber-500 flex items-center gap-1">
                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                Low balance - consider purchasing more tokens
            </p>
        @endif
    </div>

@else
    {{-- Default: Inline badge --}}
    <div {{ $attributes->merge(['class' => 'flex items-center gap-2 px-4 py-2 rounded-xl bg-muted/50 border border-border']) }}>
        <i data-lucide="coins" class="w-4 h-4 text-primary"></i>
        <span class="text-sm font-bold">{{ number_format($tokenBalance) }}</span>
        <span class="text-xs text-muted-foreground">tokens</span>
    </div>
@endif
