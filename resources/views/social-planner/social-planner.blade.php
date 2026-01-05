@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="socialPlanner()">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Campaign Command Center</h1>
            <p class="text-muted-foreground font-medium italic">Orchestrate and monitor your cross-platform social architecture.</p>
        </div>
        <button @click="showConnectModal = true" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="share-2" class="w-4 h-4"></i>
            Authorize Nodes
        </button>
    </div>

    <!-- Active Connection Nodes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        @foreach($socialConfig as $platform => $config)
            <div class="bg-card border border-border rounded-[32px] p-6 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
                @php
                    $platformColors = [
                        'facebook' => 'blue-600',
                        'instagram' => 'pink-600',
                        'linkedin' => 'blue-800',
                        'twitter' => 'sky-400'
                    ];
                    $color = $platformColors[$platform] ?? 'slate-500';
                @endphp
                
                <div class="flex items-center justify-between mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $color }}/10 flex items-center justify-center text-{{ $color }}">
                        <i data-lucide="{{ $platform }}" class="w-6 h-6"></i>
                    </div>
                    @if($config['connected'])
                        <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-green-500/10 text-green-500 border border-green-500/20 animate-in fade-in zoom-in-95">
                            <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-[8px] font-black uppercase tracking-widest">Active</span>
                        </div>
                    @else
                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-500">Offline</span>
                    @endif
                </div>

                <h3 class="text-xl font-black text-foreground uppercase tracking-tight">{{ ucfirst($platform) }}</h3>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $config['count'] }} Registry Hits</p>
                    <span class="text-[10px] font-black text-primary">{{ $config['percentage'] }}%</span>
                </div>

                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <i data-lucide="{{ $platform }}" class="w-24 h-24 text-{{ $color }}"></i>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Structural Calendar -->
        <div class="lg:col-span-1 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Content Registry Calendar</h3>
            <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                
                <div class="flex items-center justify-between mb-8">
                    <button @click="changeMonth(-1)" class="p-2 hover:bg-muted rounded-full transition-colors">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <div class="text-center">
                        <h4 class="text-xl font-black uppercase tracking-tighter" x-text="monthName()"></h4>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Industrial Timeline</p>
                    </div>
                    <button @click="changeMonth(1)" class="p-2 hover:bg-muted rounded-full transition-colors">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="grid grid-cols-7 gap-2 text-center text-[10px] font-black text-slate-500 uppercase mb-4">
                    <div>S</div><div>M</div><div>T</div><div>W</div><div>T</div><div>F</div><div>S</div>
                </div>

                <div class="grid grid-cols-7 gap-2">
                    <template x-for="i in startDayOfWeek">
                         <div class="aspect-square opacity-10 border border-transparent"></div>
                    </template>
                    <template x-for="day in daysInMonth">
                        <div @click="viewDay(day)"
                             class="aspect-square rounded-xl border border-border/50 flex flex-col items-center justify-center relative hover:border-primary/50 cursor-pointer transition-all group"
                             :class="{ 'bg-primary text-primary-foreground border-primary': isToday(day) }">
                             <span class="text-[11px] font-black" x-text="day"></span>
                             <div class="absolute bottom-1.5 flex gap-0.5">
                                <template x-for="post in postsOnDay(day).slice(0, 3)">
                                    <div class="w-1 h-1 rounded-full" :class="post.status === 'published' ? 'bg-green-500' : 'bg-primary/40 group-hover:bg-primary'"></div>
                                </template>
                                <template x-if="postsOnDay(day).length > 3">
                                    <div class="w-1 h-1 rounded-full bg-slate-400"></div>
                                </template>
                             </div>
                        </div>
                    </template>
                </div>

                <div class="mt-8 pt-8 border-t border-border/50">
                    <button @click="openCreateModal()" class="w-full h-14 bg-muted border border-border rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-white hover:text-black transition-all flex items-center justify-center gap-3">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        New Schedule Protocol
                    </button>
                </div>
            </div>
        </div>

        <!-- Scheduled Protocols Timeline -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Active Deployment Timeline</h3>
            <div class="bg-card border border-border rounded-[40px] overflow-hidden shadow-sm">
                <div class="p-8 space-y-6 max-h-[700px] overflow-y-auto custom-scrollbar">
                    @forelse($scheduledPosts as $post)
                        @php
                            $options = $post->options ?? [];
                            $platform = $options['platform'] ?? 'generic';
                            $date = \Carbon\Carbon::parse($options['scheduled_at'] ?? $post->created_at);
                            $platformColors = [
                                'facebook' => 'blue-600',
                                'instagram' => 'pink-600',
                                'linkedin' => 'blue-800',
                                'twitter' => 'sky-400',
                                'generic' => 'slate-500'
                            ];
                            $color = $platformColors[$platform] ?? 'slate-500';
                            $postJson = htmlspecialchars(json_encode($post), ENT_QUOTES, 'UTF-8');
                        @endphp
                        <div class="p-6 rounded-3xl border border-border bg-muted/5 group hover:border-primary/30 transition-all flex items-start gap-6 relative">
                            <!-- Platform Node -->
                            <div class="w-12 h-12 rounded-2xl bg-{{ $color }}/10 flex items-center justify-center text-{{ $color }} shrink-0 border border-{{ $color }}/20">
                                <i data-lucide="{{ $platform === 'generic' ? 'share-2' : $platform }}" class="w-6 h-6"></i>
                            </div>

                            <!-- Content Core -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <h4 class="font-black text-sm uppercase tracking-tight text-foreground">{{ ucfirst($platform) }} Node</h4>
                                        <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] border {{ $post->status === 'published' ? 'text-green-500 bg-green-500/5 border-green-500/20' : 'text-primary bg-primary/5 border-primary/20' }}">
                                            {{ $post->status }}
                                        </span>
                                    </div>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ $date->format('M d • H:i') }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground font-medium italic line-clamp-2 leading-relaxed mb-4">{{ $post->result }}</p>
                                
                                @if(!empty($options['image_url']))
                                    <div class="w-32 aspect-video rounded-xl overflow-hidden border border-border shadow-sm group-hover:scale-[1.02] transition-transform">
                                        <img src="{{ $options['image_url'] }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                            </div>

                            <!-- Industrial Actions -->
                            <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if(!empty($options['original_content_id']))
                                    <a href="{{ route('content-creator.show', $options['original_content_id']) }}" 
                                       class="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-primary shadow-sm hover:scale-110 transition-all"
                                       title="View Source Content">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                @endif
                                
                                <button @click="editPost('{{ $post->id }}')" 
                                        class="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-amber-500 shadow-sm hover:bg-amber-50 hover:text-amber-600 transition-all"
                                        title="Edit Protocol">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>

                                <button @click="deletePost('{{ $post->id }}')" 
                                        class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-500 shadow-sm hover:bg-red-600 hover:text-white transition-all"
                                        title="Delete Protocol">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center opacity-30 italic">
                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-4"></i>
                            <p class="text-sm font-bold uppercase tracking-widest">Protocol timeline empty</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Day View Modal -->
    <div x-show="showDayModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showDayModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between mb-8 shrink-0">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter mb-1" x-text="formattedSelectedDate()"></h2>
                    <p class="text-sm text-muted-foreground italic" x-text="selectedDayPosts.length + ' Scheduled Protocols'"></p>
                </div>
                <button @click="showDayModal = false" class="p-2 hover:bg-muted rounded-full transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="space-y-4 overflow-y-auto flex-1 custom-scrollbar pr-2">
                <template x-for="post in selectedDayPosts" :key="post.id">
                    <div class="p-4 rounded-3xl border border-border bg-muted/5 flex items-start gap-4">
                        <!-- Poster Preview -->
                         <template x-if="post.options && post.options.image_url">
                             <div class="w-24 h-24 rounded-xl overflow-hidden border border-border shrink-0">
                                 <img :src="post.options.image_url" class="w-full h-full object-cover">
                             </div>
                         </template>
                         <template x-if="!post.options || !post.options.image_url">
                             <div class="w-24 h-24 rounded-xl bg-muted border border-border flex items-center justify-center shrink-0">
                                <i data-lucide="image-off" class="w-6 h-6 text-muted-foreground/30"></i>
                             </div>
                         </template>

                         <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-muted text-muted-foreground" x-text="new Date(post.scheduled_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded" 
                                      :class="post.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-blue-50 text-blue-700'"
                                      x-text="post.status"></span>
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-slate-100 text-slate-700" x-text="post.platform"></span>
                            </div>
                            <p class="text-sm text-foreground line-clamp-3 leading-relaxed mb-3" x-text="post.result"></p>
                            
                            <div class="flex items-center gap-2">
                                <button @click="editPost(post.id); showDayModal = false" class="px-3 py-1.5 rounded-lg border border-border text-[10px] font-bold uppercase tracking-wider hover:bg-muted transition-colors">
                                    Edit
                                </button>
                                <button @click="deletePost(post.id)" class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 text-[10px] font-bold uppercase tracking-wider hover:bg-red-50 transition-colors">
                                    Delete
                                </button>
                            </div>
                         </div>
                    </div>
                </template>
                <div x-show="selectedDayPosts.length === 0" class="text-center py-12 text-muted-foreground">
                    <i data-lucide="calendar-off" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                    <p class="text-xs font-bold uppercase tracking-widest">No protocols scheduled for this cycle.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-border mt-4 shrink-0">
                 <button @click="openCreateModal(selectedDay); showDayModal = false" class="w-full h-12 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Protocol to this Day
                </button>
            </div>
        </div>
    </div>

    <!-- Create/Edit Post Modal -->
    <div x-show="showCreatePostModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showCreatePostModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-2" x-text="isEditing ? 'Modify Protocol' : 'Schedule Protocol'"></h2>
            <p class="text-sm text-muted-foreground mb-10 italic">Inject a new content node into the campaign timeline.</p>
            
            <form @submit.prevent="schedulePost" class="space-y-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Campaign Content</label>
                    <textarea x-model="newPostContent" rows="6" class="w-full bg-muted/20 border border-border rounded-3xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Execution Date</label>
                        <input x-model="newPostDate" type="date" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Execution Time</label>
                        <input x-model="newPostTime" type="time" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                <div class="pt-6 flex flex-col gap-3">
                    <button type="submit" :disabled="isScheduling" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                        <template x-if="isScheduling">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isScheduling ? 'PROCESSING...' : (isEditing ? 'UPDATE PROTOCOL' : 'INITIATE SCHEDULE')"></span>
                    </button>
                    <button type="button" @click="showCreatePostModal = false" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Abort</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Re-using existing Connect Channels Modal but refined -->
    <div x-show="showConnectModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showConnectModal = false" class="bg-card w-full max-w-lg rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
            
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Authorize Nodes</h2>
            <p class="text-sm text-muted-foreground mb-8 italic">Link your agency's social identities to the ArchitGrid.</p>

            <div class="space-y-4">
                @foreach(['facebook' => 'blue-600', 'instagram' => 'pink-600', 'linkedin' => 'blue-800', 'twitter' => 'sky-400'] as $plat => $c)
                    <div class="flex items-center justify-between p-5 rounded-[32px] border border-border bg-muted/5 group hover:border-{{ $c }}/30 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-{{ $c }}/10 flex items-center justify-center text-{{ $c }} border border-{{ $c }}/20">
                                <i data-lucide="{{ $plat }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-sm uppercase tracking-tight text-foreground">{{ ucfirst($plat) }}</h4>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest" x-text="connectedAccounts.{{ $plat }} ? 'Identity Verified' : 'Awaiting Connection'"></p>
                            </div>
                        </div>
                        <button @click="connectAccount('{{ $plat }}')" 
                                class="px-6 py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest transition-all"
                                :class="connectedAccounts.{{ $plat }} ? 'bg-green-500/10 text-green-500 hover:bg-green-500 hover:text-white' : 'bg-primary text-primary-foreground hover:bg-primary/90'">
                            <span x-text="connectedAccounts.{{ $plat }} ? 'Sync' : 'Link'"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    window.socialPlannerConfig = @js($socialConfig);
    window.socialPlannerPosts = @js($scheduledPosts);
    
    document.addEventListener('alpine:init', () => {
        Alpine.data('socialPlanner', () => ({
            posts: (window.socialPlannerPosts || []).map(p => ({
                id: p.id,
                title: p.title || 'Untitled Post',
                status: p.status,
                result: p.result,
                platform: p.options?.platform || 'generic',
                scheduled_at: p.options?.scheduled_at || p.created_at,
                original_id: p.options?.original_content_id || null,
                options: p.options || {}
            })),
            showCreatePostModal: false,
            showConnectModal: false,
            
            // Day View Modal
            showDayModal: false,
            selectedDay: null,
            selectedDayPosts: [],

            // Edit State
            isEditing: false,
            editingPostId: null,

            newPostContent: '',
            newPostDate: '',
            newPostTime: '',
            isScheduling: false,

            connectedAccounts: {
                linkedin: window.socialPlannerConfig?.linkedin?.connected || false,
                twitter: window.socialPlannerConfig?.twitter?.connected || false,
                facebook: window.socialPlannerConfig?.facebook?.connected || false,
                instagram: window.socialPlannerConfig?.instagram?.connected || false
            },

            socialConfig: window.socialPlannerConfig || {},

            currentDate: new Date(),
            
            get daysInMonth() {
                return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate();
            },
            get startDayOfWeek() {
                return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
            },
            
            monthName() {
                return this.currentDate.toLocaleString('default', { month: 'long' }) + ' ' + this.currentDate.getFullYear();
            },

            changeMonth(offset) {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + offset, 1);
            },

            isToday(day) {
                const now = new Date();
                return day === now.getDate() && 
                       this.currentDate.getMonth() === now.getMonth() && 
                       this.currentDate.getFullYear() === now.getFullYear();
            },

            postsOnDay(day) {
                const year = this.currentDate.getFullYear();
                const month = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                const dayStr = String(day).padStart(2, '0');
                const dateStr = `${year}-${month}-${dayStr}`;
                
                return this.posts.filter(p => {
                    // scheduled_at format assumed YYYY-MM-DD HH:MM:SS
                    const postDate = p.scheduled_at.split(' ')[0]; 
                    return postDate === dateStr;
                });
            },

            viewDay(day) {
                this.selectedDay = day;
                this.selectedDayPosts = this.postsOnDay(day);
                this.showDayModal = true;
            },

            formattedSelectedDate() {
                if(!this.selectedDay) return '';
                const d = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), this.selectedDay);
                return d.toLocaleDateString('default', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            },

            openCreateModal(preselectedDay = null) {
                this.isEditing = false;
                this.editingPostId = null;
                this.newPostContent = '';
                
                const now = new Date();
                
                if (preselectedDay) {
                     const year = this.currentDate.getFullYear();
                     const month = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                     const dayStr = String(preselectedDay).padStart(2, '0');
                     this.newPostDate = `${year}-${month}-${dayStr}`;
                } else {
                     this.newPostDate = now.toISOString().split('T')[0];
                }
                
                this.newPostTime = now.toTimeString().slice(0,5);
                
                this.showCreatePostModal = true;
            },

            editPost(id) {
                const post = this.posts.find(p => p.id == id);
                if (!post) return;

                this.isEditing = true;
                this.editingPostId = id;
                this.newPostContent = post.result;
                
                const dt = new Date(post.scheduled_at);
                this.newPostDate = dt.toISOString().split('T')[0];
                this.newPostTime = dt.toTimeString().slice(0,5);

                this.showCreatePostModal = true;
            },

            deletePost(id) {
                if (!confirm('Are you sure you want to delete this scheduled post?')) return;
                
                fetch(`/social-planner/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        this.posts = this.posts.filter(p => p.id != id);
                        // If we are in the modal, update the list there too
                        if (this.showDayModal) {
                            this.selectedDayPosts = this.selectedDayPosts.filter(p => p.id != id);
                        }
                    } else {
                        alert('Failed to delete.');
                    }
                });
            },
            
            async schedulePost() {
                if (!this.newPostContent || !this.newPostDate || !this.newPostTime) {
                    alert('Please fill in all fields (Content, Date, Time)');
                    return;
                }
                
                this.isScheduling = true;
                const scheduledAt = `${this.newPostDate} ${this.newPostTime}:00`;
                
                const url = this.isEditing ? `/social-planner/${this.editingPostId}` : '/social-planner/store';
                const method = this.isEditing ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ 
                            content: this.newPostContent,
                            scheduled_at: scheduledAt,
                            platform: 'generic'
                        })
                    });
                    const data = await response.json();
                    
                    if(data.success) {
                        alert(this.isEditing ? 'Protocol updated!' : 'Schedule protocol initialized!');
                        window.location.reload();
                    } else {
                        alert('Operation failed.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error communicating with grid treasury.');
                } finally {
                    this.isScheduling = false;
                }
            },

            connectAccount(platform) {
                let url = '';
                let clientId = '';
                let redirectUri = '';
                let scope = '';
                let state = 'random_state_string';

                let config = this.socialConfig[platform];

                switch(platform) {
                    case 'facebook':
                        if (!config || !config.clientId) { alert('Missing FACEBOOK_CLIENT_ID'); return; }
                        clientId = config.clientId;
                        redirectUri = config.redirectUri || window.location.origin + '/social/callback/facebook';
                        scope = 'public_profile,email,pages_manage_posts,pages_read_engagement,pages_show_list,pages_read_user_content,business_management,instagram_basic,instagram_content_publish';
                        url = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`;
                        break;

                    case 'instagram':
                        config = this.socialConfig['facebook'];
                        if (!config || !config.clientId) { alert('To connect Instagram, please configure FACEBOOK_CLIENT_ID.'); return; }
                        clientId = config.clientId;
                        redirectUri = config.redirectUri || window.location.origin + '/social/callback/facebook'; 
                        scope = 'public_profile,email,pages_manage_posts,pages_read_engagement,pages_show_list,pages_read_user_content,business_management,instagram_basic,instagram_content_publish';
                        url = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${scope}&state=${state}`;
                        break;

                    case 'linkedin':
                        if (!config || !config.clientId) { alert('Missing LINKEDIN_CLIENT_ID'); return; }
                        clientId = config.clientId;
                        redirectUri = config.redirectUri || window.location.origin + '/social/callback/linkedin';
                        scope = 'w_member_social,r_liteprofile';
                        url = `https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`;
                        break;

                    case 'twitter':
                        if (!config || !config.clientId) { alert('Missing TWITTER_CLIENT_ID'); return; }
                        clientId = config.clientId;
                        redirectUri = config.redirectUri || window.location.origin + '/social/callback/twitter';
                        scope = 'tweet.read,tweet.write,users.read';
                        url = `https://twitter.com/i/oauth2/authorize?response_type=code&client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${scope}&state=${state}&code_challenge=challenge&code_challenge_method=plain`;
                        break;
                }

                if (url) {
                    const width = 600;
                    const height = 700;
                    const left = (screen.width / 2) - (width / 2);
                    const top = (screen.height / 2) - (height / 2);
                    window.open(url, 'ConnectChannel', `width=${width},height=${height},top=${top},left=${left}`);
                }
            },
            
            init() {
                window.addEventListener('message', (event) => {
                    if (event.data.type === 'connected') {
                        alert(`Successfully connected to ${event.data.platform}!`);
                        window.location.reload();
                    }
                });
            }
        }));
    });
</script>
@endsection
