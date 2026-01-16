{{--
    Social Planner - Main Layout
    
    Modularized from 574 lines to ~80 lines.
    Components extracted to /partials directory.
    Alpine.js logic available in /resources/js/components/social-planner.js
--}}
@extends('layouts.app')

@section('content')
<script>
    // Server-side data passed to Alpine component
    window.__socialPlannerConfig = {
        socialConfig: @js($socialConfig),
        posts: @js($scheduledPosts),
        csrfToken: @js(csrf_token())
    };
    
    document.addEventListener('alpine:init', () => {
        Alpine.data('socialPlanner', () => ({
            posts: (window.__socialPlannerConfig.posts || []).map(p => ({
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
            showDayModal: false,
            selectedDay: null,
            selectedDayPosts: [],
            isEditing: false,
            editingPostId: null,
            newPostContent: '',
            newPostDate: '',
            newPostTime: '',
            isScheduling: false,
            connectedAccounts: {
                linkedin: window.__socialPlannerConfig.socialConfig?.linkedin?.connected || false,
                twitter: window.__socialPlannerConfig.socialConfig?.twitter?.connected || false,
                facebook: window.__socialPlannerConfig.socialConfig?.facebook?.connected || false,
                instagram: window.__socialPlannerConfig.socialConfig?.instagram?.connected || false
            },
            socialConfig: window.__socialPlannerConfig.socialConfig || {},
            currentDate: new Date(),
            
            get daysInMonth() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate(); },
            get startDayOfWeek() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay(); },
            monthName() { return this.currentDate.toLocaleString('default', { month: 'long' }) + ' ' + this.currentDate.getFullYear(); },
            changeMonth(offset) { this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + offset, 1); },
            isToday(day) { const now = new Date(); return day === now.getDate() && this.currentDate.getMonth() === now.getMonth() && this.currentDate.getFullYear() === now.getFullYear(); },
            postsOnDay(day) { const y = this.currentDate.getFullYear(), m = String(this.currentDate.getMonth() + 1).padStart(2, '0'), d = String(day).padStart(2, '0'); return this.posts.filter(p => p.scheduled_at.split(' ')[0] === `${y}-${m}-${d}`); },
            viewDay(day) { this.selectedDay = day; this.selectedDayPosts = this.postsOnDay(day); this.showDayModal = true; },
            formattedSelectedDate() { if (!this.selectedDay) return ''; const d = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), this.selectedDay); return d.toLocaleDateString('default', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }); },
            openCreateModal(preselectedDay = null) { this.isEditing = false; this.editingPostId = null; this.newPostContent = ''; const now = new Date(); if (preselectedDay) { const y = this.currentDate.getFullYear(), m = String(this.currentDate.getMonth() + 1).padStart(2, '0'), d = String(preselectedDay).padStart(2, '0'); this.newPostDate = `${y}-${m}-${d}`; } else { this.newPostDate = now.toISOString().split('T')[0]; } this.newPostTime = now.toTimeString().slice(0,5); this.showCreatePostModal = true; },
            editPost(id) { const post = this.posts.find(p => p.id == id); if (!post) return; this.isEditing = true; this.editingPostId = id; this.newPostContent = post.result; const dt = new Date(post.scheduled_at); this.newPostDate = dt.toISOString().split('T')[0]; this.newPostTime = dt.toTimeString().slice(0,5); this.showCreatePostModal = true; },
            deletePost(id) { if (!confirm('Are you sure you want to delete this scheduled post?')) return; fetch(`/social-planner/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': window.__socialPlannerConfig.csrfToken, 'Accept': 'application/json' }}).then(res => res.json()).then(data => { if(data.success) { this.posts = this.posts.filter(p => p.id != id); if (this.showDayModal) this.selectedDayPosts = this.selectedDayPosts.filter(p => p.id != id); } else alert('Failed to delete.'); }); },
            async schedulePost() { if (!this.newPostContent || !this.newPostDate || !this.newPostTime) { alert('Please fill in all fields'); return; } this.isScheduling = true; const url = this.isEditing ? `/social-planner/${this.editingPostId}` : '/social-planner/store'; const method = this.isEditing ? 'PUT' : 'POST'; try { const response = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.__socialPlannerConfig.csrfToken }, body: JSON.stringify({ content: this.newPostContent, scheduled_at: `${this.newPostDate} ${this.newPostTime}:00`, platform: 'generic' }) }); const data = await response.json(); if(data.success) { alert(this.isEditing ? 'Protocol updated!' : 'Schedule protocol initialized!'); window.location.reload(); } else alert('Operation failed.'); } catch (e) { console.error(e); alert('Error communicating with grid.'); } finally { this.isScheduling = false; } },
            connectAccount(platform) { let url = '', clientId = '', redirectUri = '', scope = '', state = 'random_state_string'; let cfg = this.socialConfig[platform]; switch(platform) { case 'facebook': if (!cfg?.clientId) { alert('Missing FACEBOOK_CLIENT_ID'); return; } clientId = cfg.clientId; redirectUri = cfg.redirectUri || window.location.origin + '/social/callback/facebook'; scope = 'public_profile,email,pages_manage_posts,pages_read_engagement,pages_show_list,pages_read_user_content,business_management,instagram_basic,instagram_content_publish'; url = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`; break; case 'instagram': cfg = this.socialConfig['facebook']; if (!cfg?.clientId) { alert('Configure FACEBOOK_CLIENT_ID for Instagram.'); return; } clientId = cfg.clientId; redirectUri = cfg.redirectUri || window.location.origin + '/social/callback/facebook'; scope = 'public_profile,email,pages_manage_posts,pages_read_engagement,pages_show_list,pages_read_user_content,business_management,instagram_basic,instagram_content_publish'; url = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${scope}&state=${state}`; break; case 'linkedin': if (!cfg?.clientId) { alert('Missing LINKEDIN_CLIENT_ID'); return; } clientId = cfg.clientId; redirectUri = cfg.redirectUri || window.location.origin + '/social/callback/linkedin'; scope = 'w_member_social,r_liteprofile'; url = `https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`; break; case 'twitter': if (!cfg?.clientId) { alert('Missing TWITTER_CLIENT_ID'); return; } clientId = cfg.clientId; redirectUri = cfg.redirectUri || window.location.origin + '/social/callback/twitter'; scope = 'tweet.read,tweet.write,users.read'; url = `https://twitter.com/i/oauth2/authorize?response_type=code&client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${scope}&state=${state}&code_challenge=challenge&code_challenge_method=plain`; break; } if (url) { const w = 600, h = 700, l = (screen.width / 2) - (w / 2), t = (screen.height / 2) - (h / 2); window.open(url, 'ConnectChannel', `width=${w},height=${h},top=${t},left=${l}`); } },
            init() { window.addEventListener('message', (event) => { if (event.data.type === 'connected') { alert(`Successfully connected to ${event.data.platform}!`); window.location.reload(); } }); }
        }));
    });
</script>

<div class="p-8 max-w-7xl mx-auto" x-data="socialPlanner()">
    {{-- Header --}}
    @include('social-planner.partials.header')

    {{-- Platform Connection Nodes --}}
    @include('social-planner.partials.platform-nodes')

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- Calendar Widget --}}
        @include('social-planner.partials.calendar-widget')

        {{-- Timeline Feed --}}
        @include('social-planner.partials.timeline-feed')
    </div>

    {{-- Modals --}}
    @include('social-planner.partials.modals.day-view-modal')
    @include('social-planner.partials.modals.create-post-modal')
    @include('social-planner.partials.modals.connect-modal')
</div>
@endsection
