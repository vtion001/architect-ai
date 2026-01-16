/**
 * Social Planner Alpine.js Component
 * 
 * Handles social media scheduling functionality:
 * - Calendar navigation and display
 * - Post scheduling, editing, and deletion
 * - OAuth platform connections
 * - Day view modal management
 */

/**
 * Factory function to create the social planner component
 * @param {Object} config - Configuration with socialConfig, posts, csrfToken
 * @returns {Object} - Alpine.js component data object
 */
export function createSocialPlannerComponent(config) {
    return {
        posts: (config.posts || []).map(p => ({
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
            linkedin: config.socialConfig?.linkedin?.connected || false,
            twitter: config.socialConfig?.twitter?.connected || false,
            facebook: config.socialConfig?.facebook?.connected || false,
            instagram: config.socialConfig?.instagram?.connected || false
        },

        socialConfig: config.socialConfig || {},
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
            if (!this.selectedDay) return '';
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

            this.newPostTime = now.toTimeString().slice(0, 5);
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
            this.newPostTime = dt.toTimeString().slice(0, 5);

            this.showCreatePostModal = true;
        },

        deletePost(id) {
            if (!confirm('Are you sure you want to delete this scheduled post?')) return;

            fetch(`/social-planner/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': config.csrfToken, 'Accept': 'application/json' }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.posts = this.posts.filter(p => p.id != id);
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
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                    body: JSON.stringify({
                        content: this.newPostContent,
                        scheduled_at: scheduledAt,
                        platform: 'generic'
                    })
                });
                const data = await response.json();

                if (data.success) {
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

            let platformConfig = this.socialConfig[platform];

            switch (platform) {
                case 'facebook':
                    if (!platformConfig || !platformConfig.clientId) { alert('Missing FACEBOOK_CLIENT_ID'); return; }
                    clientId = platformConfig.clientId;
                    redirectUri = platformConfig.redirectUri || window.location.origin + '/social/callback/facebook';
                    scope = 'public_profile,email,pages_manage_posts,pages_read_engagement,pages_show_list,pages_read_user_content,business_management,instagram_basic,instagram_content_publish';
                    url = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`;
                    break;

                case 'instagram':
                    platformConfig = this.socialConfig['facebook'];
                    if (!platformConfig || !platformConfig.clientId) { alert('To connect Instagram, please configure FACEBOOK_CLIENT_ID.'); return; }
                    clientId = platformConfig.clientId;
                    redirectUri = platformConfig.redirectUri || window.location.origin + '/social/callback/facebook';
                    scope = 'public_profile,email,pages_manage_posts,pages_read_engagement,pages_show_list,pages_read_user_content,business_management,instagram_basic,instagram_content_publish';
                    url = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${scope}&state=${state}`;
                    break;

                case 'linkedin':
                    if (!platformConfig || !platformConfig.clientId) { alert('Missing LINKEDIN_CLIENT_ID'); return; }
                    clientId = platformConfig.clientId;
                    redirectUri = platformConfig.redirectUri || window.location.origin + '/social/callback/linkedin';
                    scope = 'w_member_social,r_liteprofile';
                    url = `https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`;
                    break;

                case 'twitter':
                    if (!platformConfig || !platformConfig.clientId) { alert('Missing TWITTER_CLIENT_ID'); return; }
                    clientId = platformConfig.clientId;
                    redirectUri = platformConfig.redirectUri || window.location.origin + '/social/callback/twitter';
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
    };
}
