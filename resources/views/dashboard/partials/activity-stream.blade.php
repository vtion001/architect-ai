{{-- Dashboard Activity Stream --}}
@props(['recentActivities'])

<div class="space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Live Activity Stream</h3>
    <div class="bg-card border border-border rounded-[32px] overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
            <thead class="bg-muted/50 text-slate-500 font-black uppercase tracking-widest border-b border-border">
                <tr>
                    <th class="p-6">User</th>
                    <th class="p-6">Action</th>
                    <th class="p-6">Details</th>
                    <th class="p-6 text-right">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                @foreach($recentActivities as $activity)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="p-6 font-bold text-foreground">{{ $activity['actor'] }}</td>
                        <td class="p-6">
                            <span class="px-2 py-1 rounded bg-muted border border-border text-[9px] font-bold uppercase tracking-widest">
                                {{ $activity['protocol'] }}
                            </span>
                        </td>
                        <td class="p-6 text-slate-500 italic">{{ $activity['context'] }}</td>
                        <td class="p-6 text-right text-slate-400 font-mono">{{ $activity['time'] }}</td>
                    </tr>
                @endforeach
                @if(empty($recentActivities))
                    <tr>
                        <td colspan="4" class="p-12 text-center text-slate-500 italic">No activity recorded yet.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
