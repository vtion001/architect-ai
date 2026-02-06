@extends('layouts.admin')

@section('title', 'Tenant Explorer')

@section('content')
<div class="grid grid-cols-1 gap-6">
    @foreach($tenants as $agency)
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-sm hover:border-red-600/30 transition-all group">
            <div class="p-6 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-white font-black shadow-lg">
                        {{ substr($agency->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $agency->name }}</h3>
                        <p class="text-xs text-slate-500 font-mono italic">/{{ $agency->slug }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-green-500 text-[10px] font-black uppercase tracking-widest">
                        {{ $agency->status }}
                    </span>
                    <a href="{{ route('admin.tenants.show', $agency) }}" class="bg-slate-800 hover:bg-red-600 text-white p-2 rounded-xl transition-all shadow-md">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <div class="p-6 bg-slate-950/20">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Agency Users</p>
                        <p class="text-2xl font-bold text-white">{{ $agency->users_count }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Sub-Accounts</p>
                        <p class="text-2xl font-bold text-white">{{ $agency->subAccounts()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-cyan-500 uppercase tracking-widest mb-2">Token Balance</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($agency->token_balance) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Created</p>
                        <p class="text-sm font-bold text-white">{{ $agency->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
