@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2 text-foreground">Access Policies</h1>
            <p class="text-muted-foreground font-medium">Manage Dynamic ABAC (Attribute-Based Access Control) policies for your workspace.</p>
        </div>
        <a href="{{ route('policies.create') }}" class="bg-primary text-primary-foreground px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="shield-plus" class="w-4 h-4"></i>
            Create Policy
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($policies as $policy)
            <div class="bg-card border border-border rounded-2xl p-6 shadow-sm flex items-center justify-between group hover:border-primary/30 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl {{ $policy->effect === 'allow' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center shadow-inner">
                        <i data-lucide="{{ $policy->effect === 'allow' ? 'unlock' : 'lock' }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
                            {{ $policy->name }}
                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-muted text-muted-foreground border">
                                Priority: {{ $policy->priority }}
                            </span>
                        </h3>
                        <p class="text-xs font-mono text-muted-foreground mt-1">{{ json_encode($policy->conditions) }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <form action="{{ route('policies.destroy', $policy) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-muted-foreground hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-muted/20 border border-dashed border-border rounded-3xl p-12 text-center">
                <i data-lucide="shield-off" class="w-12 h-12 text-muted-foreground/30 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-foreground">No active policies</h3>
                <p class="text-sm text-muted-foreground font-medium max-w-xs mx-auto mt-2 leading-relaxed">
                    Dynamic policies allow you to grant or deny access based on user attributes and resource state.
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection
