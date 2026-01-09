@extends('layouts.app')

@php
    // Pre-process posts data for JavaScript
    // Improved regex to handle various newline formats and optional spacing around separators
    $rawSegments = preg_split('/[\r\n]+\s*-{3,}\s*[\r\n]+/', $content->result ?? '');
    $postsData = [];
    $globalHashtags = '';

    if (!empty($rawSegments)) {
        $lastSegment = trim(end($rawSegments));
        if (count($rawSegments) > 1 && str_starts_with($lastSegment, '#') && strlen($lastSegment) < 300) {
            array_pop($rawSegments);
            $globalHashtags = $lastSegment;
        }
        $rawSegments = array_values(array_filter($rawSegments));
    } else {
        $rawSegments = [$content->result ?? 'No content generated.'];
    }

    foreach ($rawSegments as $idx => $post) {
        $finalPostContent = trim($post);
        $finalPostContent = preg_replace('/^\d+\.\s*/', '', $finalPostContent);
        if ($globalHashtags) {
            $finalPostContent .= "\n\n" . $globalHashtags;
        }
        $cleanText = preg_replace('/^#+\s+/m', '', $finalPostContent);
        $cleanText = str_replace(['*', '`'], '', $cleanText);
        $cleanHtml = nl2br(e($cleanText));
        
        $postsData[] = [
            'index' => $idx,
            'raw' => $finalPostContent,
            'html' => $cleanHtml,
            'published' => in_array($idx, $publishedIndexes ?? [])
        ];
    }
@endphp

@section('content')
@if($content->status === 'generating')
    <div class="min-h-[80vh] flex flex-col items-center justify-center p-10">
        <div class="relative w-24 h-24 mb-8">
            <div class="absolute inset-0 border-t-2 border-primary rounded-full animate-spin"></div>
            <div class="absolute inset-2 border-r-2 border-purple-500 rounded-full animate-spin" style="animation-duration: 1.5s"></div>
            <div class="absolute inset-4 border-b-2 border-cyan-500 rounded-full animate-spin" style="animation-direction: reverse"></div>
        </div>
        <h2 class="text-2xl font-black uppercase tracking-tight text-slate-800">Architecting Content</h2>
        <p class="text-sm font-mono text-slate-500 mt-2 uppercase tracking-widest animate-pulse">
            {{ $content->type === 'video' ? 'Rendering Video Assets...' : 'Synthesizing Text & Context...' }}
        </p>
        <script>
            setTimeout(() => window.location.reload(), 3000);
        </script>
    </div>
@elseif($content->status === 'failed')
    <div class="min-h-[60vh] flex flex-col items-center justify-center animate-in fade-in duration-700">
        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-6 border border-red-100 shadow-sm">
            <i data-lucide="alert-triangle" class="w-10 h-10"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Generation Failed</h2>
        <p class="text-slate-500 mt-2 font-medium">The architect encountered an anomaly. Tokens have been refunded.</p>
        <div class="flex gap-4 mt-8" x-data="{ isDismissing: false, dismiss() { if(!confirm('Dismiss failed entry?')) return; this.isDismissing = true; fetch('{{ route('content-creator.destroy', $content->id) }}', { method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'} }).then(res => res.json()).then(data => { if(data.success) window.location.href = '{{ route('content-creator.index') }}'; else { alert(data.message); this.isDismissing = false; } }).catch(e => { console.error(e); this.isDismissing = false; }); } }">
            <a href="{{ route('content-creator.index') }}" class="px-8 py-4 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">
                Back to Dashboard
            </a>
            <button @click="dismiss" :disabled="isDismissing" class="px-8 py-4 bg-primary text-primary-foreground rounded-xl text-xs font-black uppercase tracking-widest hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 disabled:opacity-50">
                <span x-show="!isDismissing">Dismiss & Retry</span>
                <span x-show="isDismissing">Dismissing...</span>
            </button>
        </div>
    </div>
@else
<script>
    // Post data prepared server-side to avoid HTML attribute escaping issues
    window.__postsData = @json($postsData);
    
    document.addEventListener('alpine:init', () => {
        // Global state for calculations and constants
        const config = {
            deleteUrl: @js(route("content-creator.destroy", $content->id)),
            redirectUrl: @js(route("content-creator.index")),
            csrfToken: @js(csrf_token())
        };

        // Root Batch Manager component
        Alpine.data('batchManager', () => ({
            showDeleteModal: false,
            isDeleting: false,
            
            deleteBatch() {
                this.isDeleting = true;
                fetch(config.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = config.redirectUrl;
                    } else {
                        alert('Delete failed: ' + (data.message || 'Unknown error'));
                        this.isDeleting = false;
                        this.showDeleteModal = false;
                    }
                })
                .catch(e => {
                    console.error(e);
                    alert('An error occurred during deletion.');
                    this.isDeleting = false;
                });
            }
        }));

        // Individual Post Card component - now takes just the index and looks up data
        Alpine.data('postCard', (postIndex) => {
            const postData = window.__postsData[postIndex] || {};
            return {
                index: postIndex,
                showMediaOptions: false,
                imageUrl: null,
                isUploading: false,
                isGenerating: false,
                isRegenerating: false,
                isPublishing: false,
                isEditing: false,
                isPublished: postData.published || false,
                publishResult: null,
                showPublishModal: false,
                selectedPlatforms: [],
                scheduleDate: new Date().toISOString().slice(0, 16),
                facebookPages: [],
                selectedFacebookPage: null,
                isFacebookConnected: @js($isFacebookConnected),
                isFetchingPages: false,
                showPageModal: false,
                postNow: true,
                rawContent: postData.raw || '',
                htmlContent: postData.html || '',
            
            init() {
                const visuals = @js($content->options['visuals'] ?? []);
                if (visuals && visuals[this.index]) {
                    this.imageUrl = visuals[this.index];
                }
                this.$watch('imageUrl', (val) => {
                    if (val) this.persistVisual(this.index);
                });

                // Load remembered Facebook Page
                const savedPage = localStorage.getItem('arch_ai_fb_page');
                if (savedPage) {
                    try {
                        this.selectedFacebookPage = JSON.parse(savedPage);
                    } catch (e) { console.error('Error loading saved page', e); }
                }

                // Silent refresh to ensure we have latest permissions/IG IDs
                if (this.isFacebookConnected) {
                    this.refreshPageData();
                }
            },

            refreshPageData() {
                // Only attempt refresh if we have a selected page to update
                if (!this.selectedFacebookPage) return;
                
                fetch('/social-planner/facebook-pages')
                    .then(res => {
                        if (!res.ok) return { pages: [] };
                        return res.json();
                    })
                    .then(data => {
                        const pages = data.pages || [];
                        if (pages.length > 0) {
                            const freshPage = pages.find(p => p.id === this.selectedFacebookPage.id);
                            if (freshPage) {
                                this.selectedFacebookPage = freshPage;
                                localStorage.setItem('arch_ai_fb_page', JSON.stringify(freshPage));
                            }
                        }
                    })
                    .catch(() => {}); // Silently fail - this is a background refresh
            },

            toggleEdit() {
                this.isEditing = !this.isEditing;
                if (!this.isEditing) {
                    this.htmlContent = this.formatForDisplay(this.rawContent);
                }
            },

            persistVisual(idx) {
                fetch(@js(url("/content-creator/{$content->id}/save-visual")), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                    body: JSON.stringify({ index: idx, image_url: this.imageUrl })
                });
            },

            fetchFacebookPages() {
                this.isFetchingPages = true;
                this.showPageModal = true;
                
                // Always fetch fresh pages to catch new connections/accounts
                fetch('/social-planner/facebook-pages')
                    .then(res => res.json())
                    .then(data => { 
                        this.facebookPages = data.pages || []; 
                        this.isFacebookConnected = this.facebookPages.length > 0;
                        
                        if (this.facebookPages.length === 0) {
                            // If we get zero pages, check if we're even connected
                            if (!this.isFacebookConnected) {
                                alert('No connection found. Please link your Facebook account in the Social Planner.');
                                this.showPageModal = false;
                            } else {
                                alert('No Facebook pages found. Ensure you have granted "Manage Pages" permissions.');
                            }
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error communicating with the Social Engine.');
                        this.showPageModal = false;
                    })
                    .finally(() => { this.isFetchingPages = false; });
            },

            // Alias for Instagram since it uses the same FB Page connection logic
            fetchInstagramPages() {
                this.fetchFacebookPages();
            },

            fetchLinkedinPages() {
                alert('LinkedIn Company Page selection coming soon.');
            },

            fetchTwitterPages() {
                alert('Twitter Account selection coming soon.');
            },

            selectPage(page) {
                this.selectedFacebookPage = page;
                localStorage.setItem('arch_ai_fb_page', JSON.stringify(page)); // Persist selection
                this.showPageModal = false;
                if (!this.selectedPlatforms.includes('facebook')) {
                    this.selectedPlatforms.push('facebook');
                }
            },

            // Helper to detect video URLs
            isVideo(url) {
                if (!url) return false;
                return /\.(mp4|mov|avi|wmv|webm)$/i.test(url);
            },

            triggerUpload() { this.$refs.fileInput.click(); },

            handleUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.isUploading = true;

                if (file.type.startsWith('image/')) {
                    this.compressImage(file, (compressedBlob) => {
                        this.performUpload(compressedBlob, file.name);
                    });
                } else {
                    // Direct upload for videos
                    this.performUpload(file, file.name);
                }
            },

            performUpload(fileBlob, fileName) {
                const formData = new FormData();
                formData.append('file', fileBlob, fileName);
                fetch('/content-creator/upload-media', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': @js(csrf_token()), 'Accept': 'application/json' }
                })
                .then(async res => {
                    if (!res.ok) {
                        if (res.status === 413) throw new Error('File is too large.');
                        const text = await res.text();
                        try { return JSON.parse(text); } catch { throw new Error(res.statusText); }
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        this.imageUrl = data.url;
                        this.showMediaOptions = false;
                    } else { alert(data.message || 'Upload failed'); }
                })
                .catch(err => { console.error(err); alert(err.message || 'Upload error'); })
                .finally(() => { this.isUploading = false; });
            },

            compressImage(file, callback) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const MAX_WIDTH = 1920;
                        const MAX_HEIGHT = 1920;
                        let width = img.width;
                        let height = img.height;
                        if (width > height) {
                            if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; }
                        } else {
                            if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        canvas.toBlob((blob) => { callback(blob); }, 'image/jpeg', 0.8);
                    };
                };
            },

            generateImage() {
                this.isGenerating = true;
                const prompt = this.rawContent.substring(0, 400);
                fetch('/content-creator/generate-media', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                    body: JSON.stringify({ prompt: prompt })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) { this.imageUrl = data.url; this.showMediaOptions = false; }
                    else { alert(data.message || 'Generation failed'); }
                })
                .catch(err => alert('Generation error'))
                .finally(() => { this.isGenerating = false; });
            },

            regenerateText() {
                this.isRegenerating = true;
                fetch('/content-creator/regenerate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                    body: JSON.stringify({ content_id: @js($content->id), current_text: this.rawContent })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.rawContent = data.new_text;
                        this.htmlContent = this.formatForDisplay(data.new_text);
                    } else { alert('Failed to redo'); }
                })
                .catch(e => console.error(e))
                .finally(() => { this.isRegenerating = false; });
            },

            formatForDisplay(text) {
                let clean = text
                    .replace(/\*\*/g, '')
                    .replace(/\*/g, '')
                    .replace(/^#+\s+/gm, '')
                    .replace(/`/g, '');
                return clean.replace(/\n/g, '<br>');
            },

            openPublishModal() { this.showPublishModal = true; },

            confirmPublish() {
                if (this.selectedPlatforms.length === 0) {
                    alert('Please select at least one platform.');
                    return;
                }

                if (this.selectedPlatforms.includes('instagram') && !this.imageUrl) {
                    alert('Instagram posts require an image. Please add visuals before publishing.');
                    return;
                }

                let fbPageId = null;
                let fbPageToken = null;
                let igAccountId = null;

                if (this.selectedPlatforms.includes('facebook') || this.selectedPlatforms.includes('instagram')) {
                    if (this.selectedFacebookPage) {
                        fbPageId = this.selectedFacebookPage.id;
                        fbPageToken = this.selectedFacebookPage.access_token;
                        
                        if (this.selectedFacebookPage.instagram_business_account) {
                            igAccountId = this.selectedFacebookPage.instagram_business_account.id;
                        }
                    } else {
                        if (!confirm('You have selected a Meta platform but no specific Page. Continue?')) { return; }
                    }
                }

                if (this.selectedPlatforms.includes('instagram') && !igAccountId) {
                     if (!confirm('The selected Facebook Page does not have a linked Instagram Business account. Instagram posting will fail. Continue?')) { return; }
                }

                this.isPublishing = true;
                fetch('/content-creator/publish', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                    body: JSON.stringify({
                        content_id: @js($content->id),
                        segment_index: this.index,
                        final_text: this.rawContent,
                        image_url: this.imageUrl,
                        platforms: this.selectedPlatforms,
                        scheduled_at: this.postNow ? 'now' : this.scheduleDate,
                        facebook_page_id: fbPageId,
                        facebook_page_token: fbPageToken,
                        instagram_account_id: igAccountId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.isPublished = true;
                        this.publishResult = data.message;
                        if (data.results && data.results.facebook && !data.results.facebook.success) {
                            this.publishResult += "\n\nNote: Facebook post failed: " + data.results.facebook.error;
                        }
                        if (data.results && data.results.instagram && !data.results.instagram.success) {
                            this.publishResult += "\n\nNote: Instagram post failed: " + data.results.instagram.error;
                        }
                    } else {
                        alert('Publishing failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .finally(() => { this.isPublishing = false; this.showPublishModal = false; });
            }
        };});
    });
</script>

<div class="p-8 max-w-5xl mx-auto" x-data="batchManager()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('content-creator.index') }}" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Creator
            </a>
            <h1 class="text-3xl font-bold">{{ $content->title }}</h1>
            <p class="text-muted-foreground mt-1">{{ ucwords(str_replace('-', ' ', $content->type)) }} • {{ $content->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <button @click="showDeleteModal = true" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                Delete Batch
            </button>
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs">
                <i data-lucide="copy" class="w-4 h-4 mr-2"></i>
                Copy All
            </button>
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs shadow-lg shadow-primary/20">
                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                Publish All
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Length</p>
            <p class="text-2xl font-bold text-blue-500">{{ $content->word_count }} Words</p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Topic</p>
            <p class="text-lg font-bold truncate">{{ $content->topic }}</p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Status</p>
            <p class="text-2xl font-bold text-purple-500 uppercase">{{ $content->status }}</p>
        </div>
    </div>

    <!-- Social Media Feed Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-20">
        @foreach($postsData as $postInfo)
        <div x-data="postCard({{ $postInfo['index'] }})" 
             class="w-full bg-card border border-border rounded-xl shadow-md overflow-visible animate-in fade-in slide-in-from-bottom-4 duration-500 relative" 
             style="animation-delay: {{ $postInfo['index'] * 150 }}ms;">
            
            <!-- Index Badge -->
            <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-black shadow-lg border-4 border-background z-20">
                {{ $postInfo['index'] + 1 }}
            </div>
            
            <!-- Success Overlay -->
            <div x-show="isPublished" x-transition.opacity class="absolute inset-0 z-40 bg-white/90 backdrop-blur-[2px] flex flex-col items-center justify-center p-6 text-center" style="display: none;">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4 scale-110">
                    <i data-lucide="check-circle-2" class="w-10 h-10 text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-foreground">Done!</h3>
                <p class="text-sm text-green-700 font-medium mb-6" x-text="publishResult"></p>
                <div class="flex gap-2">
                    <button @click="isPublished = false" class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-muted-foreground border border-border rounded-lg hover:bg-muted/50 transition-all">
                        Edit / Re-publish
                    </button>
                    <a href="{{ route('social-planner.index') }}" class="px-4 py-2 text-xs font-bold uppercase tracking-widest bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-all shadow-md">
                        View in Planner
                    </a>
                </div>
            </div>
            
            <!-- Hidden File Input -->
            <input type="file" x-ref="fileInput" class="hidden" accept="image/*,video/*" @change="handleUpload">

            <!-- Post Header -->
            <div class="p-4 flex items-center justify-between border-b border-border/50">
                <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border border-primary/10 shadow-inner">
                        <i data-lucide="bot" class="w-5 h-5 text-primary"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-foreground leading-tight">ArchitGrid</h4>
                        <div class="flex items-center gap-1.5 text-[10px] text-muted-foreground font-medium">
                            <span class="text-primary font-bold">Recommended for you</span>
                            <span class="text-muted-foreground/50">•</span>
                            <span>{{ $content->created_at->diffForHumans() }}</span>
                            <span class="text-muted-foreground/50">•</span>
                            <i data-lucide="globe" class="w-3 h-3 opacity-70"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Preview Holder -->
                <div class="flex items-center">
                    <div :class="isPublished ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-50 text-red-600 border-red-100'" 
                         class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider transition-colors">
                        <div :class="isPublished ? 'bg-green-600' : 'bg-red-600'" class="w-1.5 h-1.5 rounded-full mr-1.5"></div>
                        <span x-text="isPublished ? 'Published' : 'Unpublished'"></span>
                    </div>
                </div>
            </div>

            <!-- Post Content Body -->
            <div class="p-4 text-foreground">
                <div x-show="!isEditing" class="prose prose-slate max-w-none dark:prose-invert prose-p:my-2 prose-headings:my-3 prose-ul:my-2 text-[15px] leading-relaxed" x-html="htmlContent">
                </div>
                <textarea x-show="isEditing" x-model="rawContent" class="w-full h-64 p-3 bg-muted/20 border border-border rounded-lg text-sm focus:ring-1 focus:ring-primary outline-none resize-y font-mono" placeholder="Edit your post content..." x-cloak></textarea>
            </div>

            <!-- Media Placeholder / Interactive Area -->
            <div class="px-4 pb-4">
                 <!-- Image Display State -->
                 <div x-show="imageUrl" class="relative w-full h-auto rounded-lg overflow-hidden border border-border group min-h-[200px]" x-transition>
                     <template x-if="isVideo(imageUrl)">
                         <video :src="imageUrl" controls class="w-full h-auto object-cover max-h-[500px]" x-ref="postVideo"></video>
                     </template>
                     <template x-if="!isVideo(imageUrl)">
                         <img 
                             :src="imageUrl" 
                             class="w-full h-auto object-cover max-h-[500px]" 
                             alt="Post Media"
                             x-ref="postImage"
                             @@error="$el.style.display='none'; $refs.imageErrorFallback.style.display='flex'"
                         >
                     </template>
                     <!-- Expired Image Fallback -->
                     <div x-ref="imageErrorFallback" class="hidden w-full h-48 bg-gradient-to-br from-slate-800 to-slate-900 items-center justify-center flex-col gap-3 rounded-lg border border-red-500/20">
                         <i data-lucide="image-off" class="w-10 h-10 text-red-400/50"></i>
                         <p class="text-[10px] font-black uppercase text-red-400/70 tracking-widest">Image Expired</p>
                         <button @click="showMediaOptions = true" class="px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold rounded-lg transition-colors">
                             Regenerate Visual
                         </button>
                     </div>
                     <!-- Hover Overlay to Remove/Replace -->
                     <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                         <button @click="imageUrl = null" class="p-2 bg-white/10 hover:bg-white/20 text-white rounded-full backdrop-blur-md transition-colors" title="Remove Image">
                             <i data-lucide="trash-2" class="w-5 h-5"></i>
                         </button>
                         <button @click="showMediaOptions = true" class="p-2 bg-white/10 hover:bg-white/20 text-white rounded-full backdrop-blur-md transition-colors" title="Replace Image">
                             <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                         </button>
                     </div>
                 </div>

                 <!-- Placeholder State -->
                 <div x-show="!imageUrl" class="relative w-full h-56 rounded-lg bg-muted/20 border-2 border-dashed border-border/50 overflow-hidden group">
                     
                     <!-- Default State: Add Visuals Prompt -->
                     <div @click="showMediaOptions = true" x-show="!showMediaOptions && !isGenerating && !isUploading" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-muted-foreground/50 transition-colors hover:bg-muted/30 hover:text-primary/70 cursor-pointer">
                         <div class="w-12 h-12 rounded-full bg-background/50 flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
                            <i data-lucide="image-plus" class="w-6 h-6"></i>
                         </div>
                         <span class="text-xs font-bold uppercase tracking-widest">Add Visuals</span>
                     </div>
                     
                     <!-- Loading State -->
                     <div x-show="isGenerating || isUploading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-muted/10 z-20" style="display: none;">
                        <i data-lucide="loader-2" class="w-8 h-8 text-primary animate-spin"></i>
                        <span class="text-xs font-bold uppercase tracking-widest text-primary" x-text="isGenerating ? 'Designing...' : 'Uploading...'"></span>
                     </div>

                     <!-- Active State: Media Options -->
                     <div x-show="showMediaOptions && !isGenerating && !isUploading" 
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 scale-95"
                          x-transition:enter-end="opacity-100 scale-100"
                          class="absolute inset-0 bg-background/95 backdrop-blur-sm z-10 flex flex-col items-center justify-center p-6 gap-3"
                          style="display: none;">
                        
                        <h5 class="text-sm font-semibold text-foreground mb-1">Select Media Source</h5>
                        
                        <div class="flex items-center gap-3 w-full max-w-xs">
                             <button @click="triggerUpload" class="flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-border bg-card hover:border-primary/50 hover:bg-primary/5 transition-all group/btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="paperclip" class="lucide lucide-paperclip w-5 h-5 text-muted-foreground group-hover/btn:text-primary"><path d="m16 6-8.414 8.586a2 2 0 0 0 2.829 2.829l8.414-8.586a4 4 0 1 0-5.657-5.657l-8.379 8.551a6 6 0 1 0 8.485 8.485l8.379-8.551"></path></svg>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground group-hover/btn:text-primary">Upload Photo / Video</span>
                             </button>
                             <button @click="generateImage" class="flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-border bg-card hover:border-purple-500/50 hover:bg-purple-500/5 transition-all group/btn">
                                <i data-lucide="sparkles" class="w-5 h-5 text-purple-500"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground group-hover/btn:text-purple-600">Banana Pro AI</span>
                             </button>
                        </div>

                        <button @click="showMediaOptions = false" class="absolute top-2 right-2 p-2 text-muted-foreground hover:text-foreground">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                     </div>
                 </div>
            </div>

             <!-- Draft Actions Footer -->
            <div class="px-4 py-3 border-t border-border bg-muted/5 flex items-center justify-end gap-3">
                 <button @click="toggleEdit()" class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-primary hover:border-primary/30 transition-all text-xs font-bold uppercase tracking-wider" title="Edit Content">
                    <i x-show="!isEditing" data-lucide="pencil" class="w-4 h-4"></i>
                    <i x-show="isEditing" data-lucide="check" class="w-4 h-4"></i>
                    <span x-text="isEditing ? 'Done' : 'Edit'"></span>
                </button>
                 <button @click="regenerateText" :disabled="isRegenerating" class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all text-xs font-bold uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed" title="Regenerate Text">
                    <i x-show="!isRegenerating" data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <i x-show="isRegenerating" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    <span x-text="isRegenerating ? 'Redoing...' : 'Redo'" class="hidden sm:inline"></span>
                </button>
                <button @click="openPublishModal" :disabled="isPublishing" class="flex items-center justify-center gap-2 py-2 px-4 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-all text-xs font-bold uppercase tracking-wider shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <i x-show="!isPublishing" data-lucide="send" class="w-4 h-4"></i>
                    <i x-show="isPublishing" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    <span x-text="isPublishing ? 'Publishing...' : 'Publish'"></span>
                </button>
            </div>

            <!-- Simple Publish Modal (Fixed to screen for centering) -->
            <div x-show="showPublishModal" 
                 x-cloak
                 class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
                 x-transition>
                <div @click.away="showPublishModal = false" class="bg-card w-full max-w-sm rounded-xl shadow-2xl border border-border p-5 space-y-4">
                    <div class="text-center">
                        <h3 class="text-lg font-bold">Publish to Social Planner</h3>
                        <p class="text-xs text-muted-foreground">Where should this content go?</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- LinkedIn -->
                        <div class="relative">
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" :class="{'border-blue-500 bg-blue-500/10': selectedPlatforms.includes('linkedin')}">
                                <input type="checkbox" value="linkedin" x-model="selectedPlatforms" class="hidden">
                                <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-white">Li</div>
                                <span class="text-sm font-medium">LinkedIn</span>
                            </label>
                            <button @click.stop="fetchLinkedinPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-blue-600/20 rounded-full transition-colors z-10" title="Select Page">
                                <i data-lucide="settings" class="w-4 h-4 text-muted-foreground"></i>
                            </button>
                        </div>

                        <!-- Twitter -->
                        <div class="relative">
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" :class="{'border-sky-500 bg-sky-500/10': selectedPlatforms.includes('twitter')}">
                                <input type="checkbox" value="twitter" x-model="selectedPlatforms" class="hidden">
                                <div class="w-8 h-8 rounded bg-sky-400 flex items-center justify-center text-white">Tw</div>
                                <span class="text-sm font-medium">Twitter</span>
                            </label>
                            <button @click.stop="fetchTwitterPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-sky-400/20 rounded-full transition-colors z-10" title="Select Account">
                                <i data-lucide="settings" class="w-4 h-4 text-muted-foreground"></i>
                            </button>
                        </div>

                        <!-- Facebook -->
                         <div class="relative" :class="!isFacebookConnected && 'opacity-60 cursor-not-allowed'">
                             <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" 
                                    :class="{'border-blue-700 bg-blue-700/10': selectedPlatforms.includes('facebook')}"
                                    @click="if(!isFacebookConnected) { alert('Connect Facebook in Social Planner first'); return false; }">
                                <input type="checkbox" value="facebook" x-model="selectedPlatforms" class="hidden" :disabled="!isFacebookConnected">
                                <div class="w-8 h-8 rounded bg-blue-700 flex items-center justify-center text-white">Fb</div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">Facebook</span>
                                    <span x-show="!isFacebookConnected" class="text-[9px] text-red-500 font-bold uppercase tracking-tighter">Not Connected</span>
                                    <span x-show="isFacebookConnected && selectedFacebookPage" class="text-[9px] text-blue-600 font-bold uppercase tracking-tighter truncate max-w-[100px]" x-text="selectedFacebookPage?.name"></span>
                                </div>
                            </label>
                             <button @click.stop="fetchFacebookPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-blue-200/20 rounded-full transition-colors z-10" title="Select Page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="settings" class="lucide lucide-settings w-4 h-4 text-muted-foreground"><path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"></path><circle cx="12" cy="12" r="3"></circle></svg>
                             </button>
                         </div>
                         
                         <!-- Selected Page Indicator (Shared/FB) -->
                        <div x-show="selectedFacebookPage" class="col-span-2 text-xs text-center p-2 bg-blue-50 text-blue-800 rounded-lg flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                            Posting to: <strong x-text="selectedFacebookPage?.name"></strong>
                        </div>

                        <!-- Instagram -->
                        <div class="relative" :class="!isFacebookConnected && 'opacity-60 cursor-not-allowed'">
                             <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" :class="{'border-pink-500 bg-pink-500/10': selectedPlatforms.includes('instagram')}">
                                 <input type="checkbox" value="instagram" x-model="selectedPlatforms" class="hidden" :disabled="!isFacebookConnected">
                                <div class="w-8 h-8 rounded bg-pink-600 flex items-center justify-center text-white">In</div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">Instagram</span>
                                    <span x-show="isFacebookConnected && selectedFacebookPage" class="text-[9px] text-pink-600 font-bold uppercase tracking-tighter truncate max-w-[100px]" x-text="selectedFacebookPage?.name"></span>
                                </div>
                            </label>
                            <button @click.stop="fetchInstagramPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-pink-500/20 rounded-full transition-colors z-10" title="Select Account">
                                <i data-lucide="settings" class="w-4 h-4 text-muted-foreground"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 py-1">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold uppercase text-muted-foreground">Timing</label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <span class="text-[10px] font-bold uppercase transition-colors" :class="postNow ? 'text-primary' : 'text-muted-foreground'">Post Now</span>
                                <div class="relative w-8 h-4 bg-muted rounded-full transition-colors" :class="postNow && 'bg-primary/20'">
                                    <input type="checkbox" x-model="postNow" class="sr-only">
                                    <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full transition-transform shadow-sm" :class="postNow && 'translate-x-4 bg-primary'"></div>
                                </div>
                            </label>
                        </div>
                        
                        <div x-show="!postNow" x-transition>
                             <input type="datetime-local" x-model="scheduleDate" class="w-full bg-muted/30 border border-border rounded-lg text-sm px-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button @click="showPublishModal = false" class="flex-1 py-2 text-xs font-bold uppercase text-muted-foreground hover:bg-muted rounded-lg">Cancel</button>
                        <button @click="confirmPublish" class="flex-1 py-2 text-xs font-bold uppercase bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 shadow-md">Confirm</button>
                    </div>

                    <!-- Page Selection Modal Overlay -->
                    <div x-show="showPageModal" 
                         x-cloak
                         class="absolute inset-0 z-50 bg-background/80 backdrop-blur-sm flex items-center justify-center p-4 rounded-xl" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                        
                        <div class="bg-card w-full max-w-[300px] max-h-[90%] rounded-2xl shadow-2xl border border-border p-5 flex flex-col overflow-hidden">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-sm text-foreground">Select Page</h4>
                                    <p class="text-[10px] text-muted-foreground uppercase tracking-widest font-black">Choose destination</p>
                                </div>
                                <button @click="showPageModal = false" class="p-1.5 hover:bg-muted rounded-full transition-colors text-muted-foreground hover:text-foreground">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                            
                            <div x-show="isFetchingPages" class="flex-1 py-12 flex flex-col items-center justify-center gap-3">
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-full border-2 border-primary/20 border-t-primary animate-spin"></div>
                                    <i data-lucide="facebook" class="w-4 h-4 text-primary absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></i>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground animate-pulse">Fetching Pages...</span>
                            </div>

                            <div x-show="!isFetchingPages" class="flex-1 overflow-y-auto space-y-1.5 pr-1 custom-scrollbar">
                                <template x-for="page in facebookPages" :key="page.id">
                                    <button @click="selectPage(page)" 
                                            class="w-full text-left p-2.5 rounded-xl border border-border hover:bg-muted/50 flex items-center gap-3 transition-all group relative overflow-hidden" 
                                            :class="{'bg-primary/5 border-primary/30 ring-1 ring-primary/10 shadow-sm': selectedFacebookPage && selectedFacebookPage.id === page.id}">
                                        
                                        <!-- Selected Indicator Glow -->
                                        <div x-show="selectedFacebookPage && selectedFacebookPage.id === page.id" class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent"></div>

                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white text-xs font-black shadow-md group-hover:scale-105 transition-transform shrink-0" x-text="page.name.charAt(0)"></div>
                                        
                                        <div class="flex-1 min-w-0 relative">
                                            <p class="text-[11px] font-bold text-foreground truncate leading-tight" x-text="page.name"></p>
                                            <p class="text-[9px] text-muted-foreground truncate uppercase tracking-tighter font-semibold" x-text="page.category || 'Facebook Page'"></p>
                                        </div>

                                        <div x-show="selectedFacebookPage && selectedFacebookPage.id === page.id" class="w-5 h-5 rounded-full bg-primary flex items-center justify-center shrink-0 shadow-sm">
                                            <i data-lucide="check" class="w-3 h-3 text-white"></i>
                                        </div>
                                    </button>
                                </template>
                                
                                <div x-show="facebookPages.length === 0" class="text-center py-8 px-4 bg-muted/20 rounded-xl border border-dashed border-border">
                                    <i data-lucide="alert-circle" class="w-8 h-8 text-muted-foreground/40 mx-auto mb-2"></i>
                                    <p class="text-[10px] font-bold text-muted-foreground leading-relaxed">
                                        No pages found.<br>
                                        <span class="font-medium opacity-70">Ensure permissions are granted in Social Planner.</span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-border flex justify-center">
                                <button @click="showPageModal = false" class="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-primary transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Simple Delete Batch Confirmation Modal -->
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="!isDeleting && (showDeleteModal = false)" 
             class="bg-card w-full max-w-md rounded-2xl shadow-2xl border border-border p-8 text-center animate-in zoom-in-95 duration-300">
            
            <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                <i data-lucide="alert-triangle" class="w-10 h-10 text-red-600"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-foreground mb-2">Delete Entire Batch?</h2>
            <p class="text-muted-foreground mb-8 leading-relaxed">
                This action is <span class="text-red-600 font-bold uppercase tracking-tight">permanent</span>. It will remove the local record and attempt to delete all associated posts from your social media platforms.
            </p>
            
            <div class="flex flex-col gap-3">
                <button @click="deleteBatch()" 
                        :disabled="isDeleting"
                        class="w-full py-4 rounded-xl bg-red-600 text-white font-bold uppercase tracking-wider text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-200 disabled:opacity-50 flex items-center justify-center gap-2">
                    <template x-if="isDeleting">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    </template>
                    <span x-text="isDeleting ? 'Removing everything...' : 'Yes, Delete Batch'"></span>
                </button>
                <button @click="showDeleteModal = false" 
                        :disabled="isDeleting"
                        class="w-full py-4 rounded-xl bg-muted text-muted-foreground font-bold uppercase tracking-wider text-sm hover:bg-muted/80 transition-all disabled:opacity-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
