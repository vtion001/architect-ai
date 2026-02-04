@extends('layouts.app')

@section('page-title', 'Billing & Subscription')

@section('content')
<div class="min-h-screen bg-grid-dark px-4 pt-24 pb-12">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-10">
            <h1 class="text-3xl font-display font-bold text-gradient-primary">Subscription & Billing</h1>
            <p class="text-grid-400 mt-2">Manage your plan, view credit usage, and upgrade your workspace.</p>
        </div>

        {{-- Current Plan Summary --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <div class="lg:col-span-2 bg-grid-800/50 border border-grid-700 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-sm text-grid-400">Current Plan</span>
                        <h2 class="text-2xl font-bold text-white">{{ $plans[$currentPlan]['name'] ?? ucfirst($currentPlan) }}</h2>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-medium 
                        {{ $currentPlan === 'agency' ? 'bg-primary-500/20 text-primary-400' : 
                           ($currentPlan === 'pro' ? 'bg-blue-500/20 text-blue-400' : 'bg-grid-600/50 text-grid-300') }}">
                        {{ ucfirst($currentPlan) }}
                    </span>
                </div>
                
                <p class="text-grid-400 mb-6">
                    {{ $plans[$currentPlan]['description'] ?? 'Your current subscription tier.' }}
                </p>

                @if($currentPlan !== 'agency')
                    <a href="{{ route('billing.upgrade') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Upgrade Plan
                    </a>
                @endif
            </div>

            {{-- Quick Stats --}}
            <div class="bg-grid-800/50 border border-grid-700 rounded-xl p-6">
                <h3 class="text-sm font-medium text-grid-400 mb-4">This Month's Usage</h3>
                <div class="space-y-4">
                    @foreach($credits as $credit)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-grid-300">{{ $credit['label'] }}</span>
                                <span class="text-grid-400">
                                    @if($credit['limit'] === -1)
                                        {{ $credit['used'] }} / ∞
                                    @else
                                        {{ $credit['used'] }} / {{ $credit['limit'] }}
                                    @endif
                                </span>
                            </div>
                            <div class="h-2 bg-grid-700 rounded-full overflow-hidden">
                                @php
                                    $percentage = $credit['limit'] === -1 ? 5 : 
                                        ($credit['limit'] > 0 ? min(100, ($credit['used'] / $credit['limit']) * 100) : 0);
                                @endphp
                                <div class="h-full rounded-full transition-all duration-500
                                    {{ $percentage >= 90 ? 'bg-red-500' : ($percentage >= 70 ? 'bg-yellow-500' : 'bg-primary-500') }}"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Plan Comparison --}}
        <div class="bg-grid-800/50 border border-grid-700 rounded-xl p-6 mb-10">
            <h3 class="text-lg font-semibold text-white mb-6">Compare Plans</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($plans as $planKey => $plan)
                    <div class="bg-grid-900/50 border rounded-xl p-6 transition-all
                        {{ $plan['current'] ? 'border-primary-500 ring-2 ring-primary-500/20' : 'border-grid-700 hover:border-grid-600' }}">
                        
                        @if($plan['current'])
                            <span class="inline-block px-2 py-0.5 text-xs bg-primary-500/20 text-primary-400 rounded mb-2">Current</span>
                        @endif
                        
                        <h4 class="text-xl font-bold text-white mb-1">{{ $plan['name'] }}</h4>
                        <p class="text-2xl font-bold text-primary-400 mb-4">
                            @if($plan['price'] === 0)
                                Free
                            @else
                                ${{ $plan['price'] }}<span class="text-sm text-grid-400">/mo</span>
                            @endif
                        </p>
                        
                        <ul class="space-y-2 text-sm">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-center gap-2 {{ $feature['included'] ? 'text-grid-300' : 'text-grid-500' }}">
                                    @if($feature['included'])
                                        <svg class="w-4 h-4 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-grid-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @endif
                                    {{ $feature['name'] }}: {{ $feature['value'] }}
                                </li>
                            @endforeach
                        </ul>
                        
                        @if(!$plan['current'] && $plan['price'] > ($plans[$currentPlan]['price'] ?? 0))
                            <a href="{{ route('billing.upgrade') }}?plan={{ $planKey }}" 
                               class="block mt-6 text-center px-4 py-2 border border-primary-500 text-primary-400 hover:bg-primary-500/10 rounded-lg transition-colors">
                                Upgrade to {{ $plan['name'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Feature Credits Detail --}}
        <div class="bg-grid-800/50 border border-grid-700 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Feature Credits</h3>
            <p class="text-grid-400 text-sm mb-6">Credits reset monthly. Unused credits do not roll over.</p>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-grid-700">
                            <th class="text-left py-3 px-4 text-grid-400 font-medium">Feature</th>
                            <th class="text-center py-3 px-4 text-grid-400 font-medium">Used</th>
                            <th class="text-center py-3 px-4 text-grid-400 font-medium">Limit</th>
                            <th class="text-center py-3 px-4 text-grid-400 font-medium">Remaining</th>
                            <th class="text-right py-3 px-4 text-grid-400 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($credits as $credit)
                            @php
                                $remaining = $credit['limit'] === -1 ? '∞' : max(0, $credit['limit'] - $credit['used']);
                                $status = $credit['limit'] === -1 ? 'unlimited' : 
                                    ($credit['used'] >= $credit['limit'] ? 'exhausted' : 'available');
                            @endphp
                            <tr class="border-b border-grid-700/50 hover:bg-grid-700/20">
                                <td class="py-3 px-4 text-grid-300">{{ $credit['label'] }}</td>
                                <td class="py-3 px-4 text-center text-grid-400">{{ $credit['used'] }}</td>
                                <td class="py-3 px-4 text-center text-grid-400">
                                    {{ $credit['limit'] === -1 ? '∞' : $credit['limit'] }}
                                </td>
                                <td class="py-3 px-4 text-center text-grid-300">{{ $remaining }}</td>
                                <td class="py-3 px-4 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                        {{ $status === 'unlimited' ? 'bg-primary-500/20 text-primary-400' : 
                                           ($status === 'exhausted' ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
