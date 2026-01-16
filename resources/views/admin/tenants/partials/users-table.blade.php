{{-- Tenant Show - Users Table --}}
<div class="space-y-4">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Account Identities</h3>
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950/50 text-slate-500 font-black uppercase border-b border-slate-800">
                <tr>
                    <th class="p-4 px-6">User</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Last Login</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($tenant->users as $user)
                    <tr>
                        <td class="p-4 px-6">
                            <p class="font-bold text-white">{{ $user->email }}</p>
                            <p class="text-[10px] text-slate-500">{{ $user->id }}</p>
                        </td>
                        <td class="p-4">
                            <span class="text-[10px] font-bold text-slate-400">{{ $user->status }}</span>
                        </td>
                        <td class="p-4 text-slate-500">
                            {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                        </td>
                        <td class="p-4">
                            <button @click="selectedUser = @js($user); showImpersonateModal = true" class="px-3 py-1.5 rounded-lg bg-red-600/10 text-red-500 hover:bg-red-600 hover:text-white text-[10px] font-black uppercase tracking-widest transition-all">
                                Impersonate
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
