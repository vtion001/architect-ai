{{-- Users Management - Users List --}}
<div class="lg:col-span-8 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Authorized Grid Personnel</h3>
    <div class="grid grid-cols-1 gap-4">
        @foreach($users as $user)
            @include('tenant.users.partials.user-card', ['user' => $user])
        @endforeach
    </div>
</div>
