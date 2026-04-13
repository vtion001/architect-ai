@extends('layouts.app')

@section('page-title', 'Upgrade Plan')

@section('content')
<div class="min-h-screen bg-grid-dark px-4 pt-24 pb-12">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="mb-10">
            <a href="{{ route('billing.index') }}" class="inline-flex items-center gap-2 text-grid-400 hover:text-white transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Billing
            </a>
            <h1 class="text-3xl font-display font-bold text-gradient-primary">Upgrade Your Plan</h1>
            <p class="text-grid-400 mt-2">Choose the plan that best fits your needs.</p>
        </div>

        {{-- Current Plan --}}
        <div class="bg-grid-800/50 border border-grid-700 rounded-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-grid-400">Current Plan</span>
                    <h2 class="text-xl font-bold text-white">{{ ucfirst($currentPlan) }}</h2>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-medium bg-grid-600/50 text-grid-300">
                    {{ ucfirst($currentPlan) }}
                </span>
            </div>
        </div>

        {{-- Plan Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Starter --}}
            <div class="bg-grid-800/50 border border-grid-700 rounded-xl p-6 hover:border-grid-600 transition-colors">
                <h3 class="text-xl font-bold text-white mb-2">Starter</h3>
                <p class="text-3xl font-bold text-primary-400 mb-4">Free</p>
                <p class="text-grid-400 text-sm mb-6">Perfect for getting started.</p>
                <ul class="space-y-3 text-sm mb-6">
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        5 blog posts/month
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        20 social posts/month
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Basic research
                    </li>
                </ul>
                @if($currentPlan === 'starter')
                    <span class="block w-full text-center px-4 py-2 bg-grid-600/50 text-grid-300 rounded-lg text-sm font-medium">Current Plan</span>
                @else
                    <button class="w-full px-4 py-2 border border-grid-600 text-grid-300 hover:bg-grid-700/50 rounded-lg transition-colors text-sm font-medium">
                        Downgrade
                    </button>
                @endif
            </div>

            {{-- Pro --}}
            <div class="bg-grid-800/50 border-2 border-primary-500 rounded-xl p-6 relative shadow-lg shadow-primary-500/10">
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-primary-500 text-white text-xs font-bold rounded-full">POPULAR</span>
                <h3 class="text-xl font-bold text-white mb-2">Pro</h3>
                <p class="text-3xl font-bold text-primary-400 mb-4">$49<span class="text-sm text-grid-400">/mo</span></p>
                <p class="text-grid-400 text-sm mb-6">For growing businesses.</p>
                <ul class="space-y-3 text-sm mb-6">
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        50 blog posts/month
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        200 social posts/month
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Advanced research
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Priority support
                    </li>
                </ul>
                @if($currentPlan === 'pro')
                    <span class="block w-full text-center px-4 py-2 bg-primary-500/20 text-primary-400 rounded-lg text-sm font-medium">Current Plan</span>
                @else
                    <button class="w-full px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors text-sm font-bold">
                        Upgrade to Pro
                    </button>
                @endif
            </div>

            {{-- Agency --}}
            <div class="bg-grid-800/50 border border-grid-700 rounded-xl p-6 hover:border-grid-600 transition-colors">
                <h3 class="text-xl font-bold text-white mb-2">Agency</h3>
                <p class="text-3xl font-bold text-white mb-4">$149<span class="text-sm text-grid-400">/mo</span></p>
                <p class="text-grid-400 text-sm mb-6">For agencies & teams.</p>
                <ul class="space-y-3 text-sm mb-6">
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Unlimited blog posts
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Unlimited social posts
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Full research suite
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Client management
                    </li>
                    <li class="flex items-center gap-2 text-grid-300">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        White-label reports
                    </li>
                </ul>
                @if($currentPlan === 'agency')
                    <span class="block w-full text-center px-4 py-2 bg-grid-600/50 text-grid-300 rounded-lg text-sm font-medium">Current Plan</span>
                @else
                    <button class="w-full px-4 py-2 border border-primary-500 text-primary-400 hover:bg-primary-500/10 rounded-lg transition-colors text-sm font-bold">
                        Upgrade to Agency
                    </button>
                @endif
            </div>
        </div>

        {{-- FAQ --}}
        <div class="bg-grid-800/50 border border-grid-700 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Frequently Asked Questions</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="text-white font-medium mb-1">Can I cancel anytime?</h4>
                    <p class="text-grid-400 text-sm">Yes, you can cancel your subscription at any time. Your plan will remain active until the end of your billing period.</p>
                </div>
                <div>
                    <h4 class="text-white font-medium mb-1">What payment methods do you accept?</h4>
                    <p class="text-grid-400 text-sm">We accept all major credit cards, PayPal, and bank transfers for annual plans.</p>
                </div>
                <div>
                    <h4 class="text-white font-medium mb-1">Do unused credits roll over?</h4>
                    <p class="text-grid-400 text-sm">No, credits reset each month and do not roll over. Choose a plan that fits your monthly needs.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
