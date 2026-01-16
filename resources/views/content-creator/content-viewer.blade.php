{{--
    Content Viewer - Main Layout
    
    Modularized from 1,346 lines to ~200 lines.
    Post card components and modals extracted to /partials/post-card directory.
    Alpine.js logic available in /resources/js/components/content-viewer/
--}}
@extends('layouts.app')

@section('content')
@if($content->status === 'generating')
    @include('content-creator.partials.loading-state')
@elseif($content->status === 'failed')
    @include('content-creator.partials.failed-state')
@else
<script>
    // Post data prepared server-side to avoid HTML attribute escaping issues
    window.__postsData = @json($postsData);
    window.__contentViewerConfig = {
        deleteUrl: @js(route("content-creator.destroy", $content->id)),
        redirectUrl: @js(route("content-creator.index")),
        saveVisualUrl: @js(url("/content-creator/{$content->id}/save-visual")),
        csrfToken: @js(csrf_token()),
        contentId: @js($content->id),
        isFacebookConnected: @js($isFacebookConnected),
        brands: @js($brands ?? []),
        visuals: @js($content->options['visuals'] ?? [])
    };
    
    document.addEventListener('alpine:init', () => {
        // Batch Manager component
        Alpine.data('batchManager', () => ({
            showDeleteModal: false,
            isDeleting: false,
            showCopyAllToast: false,
            
            copyAllPosts() {
                const allPosts = window.__postsData.map(p => p.raw).join('\n\n---\n\n');
                navigator.clipboard.writeText(allPosts).then(() => {
                    this.showCopyAllToast = true;
                    setTimeout(() => { this.showCopyAllToast = false; }, 2500);
                }).catch(err => {
                    const textarea = document.createElement('textarea');
                    textarea.value = allPosts;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    this.showCopyAllToast = true;
                    setTimeout(() => { this.showCopyAllToast = false; }, 2500);
                });
            },
            
            deleteBatch() {
                this.isDeleting = true;
                fetch(window.__contentViewerConfig.deleteUrl, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.__contentViewerConfig.csrfToken, 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) { window.location.href = window.__contentViewerConfig.redirectUrl; }
                    else { alert('Delete failed: ' + (data.message || 'Unknown error')); this.isDeleting = false; this.showDeleteModal = false; }
                })
                .catch(e => { console.error(e); alert('An error occurred during deletion.'); this.isDeleting = false; });
            }
        }));

        // Post Card component
        Alpine.data('postCard', (postIndex) => {
            const cfg = window.__contentViewerConfig;
            const postData = window.__postsData[postIndex] || {};
            return {
                index: postIndex,
                showMediaOptions: false, imageUrl: null, isUploading: false, isGenerating: false, isRegenerating: false, isPublishing: false, isEditing: false,
                isPublished: postData.published || false, publishResult: null, showPublishModal: false, selectedPlatforms: [], scheduleDate: new Date().toISOString().slice(0, 16),
                facebookPages: [], selectedFacebookPage: null, isFacebookConnected: cfg.isFacebookConnected, isFetchingPages: false, showPageModal: false, postNow: true,
                rawContent: postData.raw || '', htmlContent: postData.html || '',
                showImageCreatorModal: false, imageFormat: 'realistic', imagePrompt: '', posterText: '', selectedAssetUrl: null, selectedBrandId: null, mediaAssets: [], isLoadingAssets: false, brands: cfg.brands, showCopyToast: false,
            
                init() {
                    if (cfg.visuals && cfg.visuals[this.index]) this.imageUrl = cfg.visuals[this.index];
                    this.$watch('imageUrl', (val) => { if (val) this.persistVisual(this.index); });
                    const savedPage = localStorage.getItem('arch_ai_fb_page');
                    if (savedPage) { try { this.selectedFacebookPage = JSON.parse(savedPage); } catch (e) {} }
                    if (this.isFacebookConnected) this.refreshPageData();
                },
                refreshPageData() { if (!this.selectedFacebookPage) return; fetch('/social-planner/facebook-pages').then(res => res.ok ? res.json() : { pages: [] }).then(data => { const pages = data.pages || []; if (pages.length > 0) { const fresh = pages.find(p => p.id === this.selectedFacebookPage.id); if (fresh) { this.selectedFacebookPage = fresh; localStorage.setItem('arch_ai_fb_page', JSON.stringify(fresh)); } } }).catch(() => {}); },
                toggleEdit() { this.isEditing = !this.isEditing; if (!this.isEditing) this.htmlContent = this.formatForDisplay(this.rawContent); },
                persistVisual(idx) { fetch(cfg.saveVisualUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ index: idx, image_url: this.imageUrl }) }); },
                fetchFacebookPages() { this.isFetchingPages = true; this.showPageModal = true; fetch('/social-planner/facebook-pages').then(res => res.json()).then(data => { this.facebookPages = data.pages || []; this.isFacebookConnected = this.facebookPages.length > 0; if (this.facebookPages.length === 0) { if (!this.isFacebookConnected) { alert('No connection. Link Facebook in Social Planner.'); this.showPageModal = false; } else alert('No pages found.'); } }).catch(err => { console.error(err); alert('Error with Social Engine.'); this.showPageModal = false; }).finally(() => { this.isFetchingPages = false; }); },
                fetchInstagramPages() { this.fetchFacebookPages(); }, fetchLinkedinPages() { alert('LinkedIn coming soon.'); }, fetchTwitterPages() { alert('Twitter coming soon.'); },
                selectPage(page) { this.selectedFacebookPage = page; localStorage.setItem('arch_ai_fb_page', JSON.stringify(page)); this.showPageModal = false; if (!this.selectedPlatforms.includes('facebook')) this.selectedPlatforms.push('facebook'); },
                isVideo(url) { if (!url) return false; return /\.(mp4|mov|avi|wmv|webm)$/i.test(url); },
                copyContent() { navigator.clipboard.writeText(this.rawContent).then(() => { this.showCopyToast = true; setTimeout(() => { this.showCopyToast = false; }, 2000); }).catch(() => { const t = document.createElement('textarea'); t.value = this.rawContent; t.style.position = 'fixed'; t.style.opacity = '0'; document.body.appendChild(t); t.select(); document.execCommand('copy'); document.body.removeChild(t); this.showCopyToast = true; setTimeout(() => { this.showCopyToast = false; }, 2000); }); },
                triggerUpload() { this.$refs.fileInput.click(); },
                handleUpload(event) { const file = event.target.files[0]; if (!file) return; this.isUploading = true; if (file.type.startsWith('image/')) { this.compressImage(file, (blob) => { this.performUpload(blob, file.name); }); } else { this.performUpload(file, file.name); } },
                performUpload(blob, name) { const fd = new FormData(); fd.append('file', blob, name); fetch('/content-creator/upload-media', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': cfg.csrfToken, 'Accept': 'application/json' } }).then(async res => { if (!res.ok) { if (res.status === 413) throw new Error('File too large.'); return res.json(); } return res.json(); }).then(data => { if (data.success) { this.imageUrl = data.url; this.showMediaOptions = false; } else alert(data.message || 'Upload failed'); }).catch(err => { console.error(err); alert(err.message || 'Upload error'); }).finally(() => { this.isUploading = false; }); },
                compressImage(file, cb) { const r = new FileReader(); r.readAsDataURL(file); r.onload = (e) => { const img = new Image(); img.src = e.target.result; img.onload = () => { const c = document.createElement('canvas'); const MAX = 1920; let w = img.width, h = img.height; if (w > h) { if (w > MAX) { h *= MAX / w; w = MAX; } } else { if (h > MAX) { w *= MAX / h; h = MAX; } } c.width = w; c.height = h; c.getContext('2d').drawImage(img, 0, 0, w, h); c.toBlob((blob) => { cb(blob); }, 'image/jpeg', 0.8); }; }; },
                openImageCreator() { let cp = this.rawContent.replace(/#\w+/g, '').replace(/\n+/g, ' ').trim().substring(0, 300); this.imagePrompt = cp; this.posterText = cp.substring(0, 80); this.showImageCreatorModal = true; this.showMediaOptions = false; if (this.mediaAssets.length === 0) this.loadMediaAssets(); },
                loadMediaAssets() { this.isLoadingAssets = true; fetch('/media-assets?limit=20', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken } }).then(res => res.json()).then(data => { this.mediaAssets = data.assets || []; }).catch(err => console.error(err)).finally(() => { this.isLoadingAssets = false; }); },
                generateAdvancedImage() { if (!this.imagePrompt.trim()) { alert('Please enter a prompt.'); return; } this.isGenerating = true; this.showImageCreatorModal = false; fetch('/content-creator/generate-media', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ prompt: this.imagePrompt, format: this.imageFormat, poster_text: this.imageFormat === 'poster' ? this.posterText : null, reference_asset_url: this.imageFormat === 'asset-reference' ? this.selectedAssetUrl : null, brand_id: this.imageFormat === 'poster' ? this.selectedBrandId : null }) }).then(res => res.json()).then(data => { if (data.success) { this.imageUrl = data.url; this.showMediaOptions = false; } else alert(data.message || 'Generation failed'); }).catch(err => { console.error(err); alert('Generation error.'); }).finally(() => { this.isGenerating = false; }); },
                generateImage() { this.openImageCreator(); },
                regenerateText() { this.isRegenerating = true; fetch('/content-creator/regenerate', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ content_id: cfg.contentId, current_text: this.rawContent }) }).then(res => res.json()).then(data => { if (data.success) { this.rawContent = data.new_text; this.htmlContent = this.formatForDisplay(data.new_text); } else alert('Failed to redo'); }).catch(e => console.error(e)).finally(() => { this.isRegenerating = false; }); },
                formatForDisplay(text) { return text.replace(/\*\*/g, '').replace(/\*/g, '').replace(/^#+\s+/gm, '').replace(/`/g, '').replace(/\n/g, '<br>'); },
                openPublishModal() { this.showPublishModal = true; },
                confirmPublish() { if (this.selectedPlatforms.length === 0) { alert('Select at least one platform.'); return; } if (this.selectedPlatforms.includes('instagram') && !this.imageUrl) { alert('Instagram requires an image.'); return; } let fbId = null, fbToken = null, igId = null; if (this.selectedPlatforms.includes('facebook') || this.selectedPlatforms.includes('instagram')) { if (this.selectedFacebookPage) { fbId = this.selectedFacebookPage.id; fbToken = this.selectedFacebookPage.access_token; if (this.selectedFacebookPage.instagram_business_account) igId = this.selectedFacebookPage.instagram_business_account.id; } else { if (!confirm('No Meta Page selected. Continue?')) return; } } if (this.selectedPlatforms.includes('instagram') && !igId) { if (!confirm('No linked IG account. Continue?')) return; } this.isPublishing = true; fetch('/content-creator/publish', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken }, body: JSON.stringify({ content_id: cfg.contentId, segment_index: this.index, final_text: this.rawContent, image_url: this.imageUrl, platforms: this.selectedPlatforms, scheduled_at: this.postNow ? 'now' : this.scheduleDate, facebook_page_id: fbId, facebook_page_token: fbToken, instagram_account_id: igId }) }).then(res => res.json()).then(data => { if (data.success) { this.isPublished = true; this.publishResult = data.message; if (data.results?.facebook && !data.results.facebook.success) this.publishResult += "\n\nNote: FB failed: " + data.results.facebook.error; if (data.results?.instagram && !data.results.instagram.success) this.publishResult += "\n\nNote: IG failed: " + data.results.instagram.error; } else alert('Publishing failed: ' + (data.message || 'Unknown error')); }).finally(() => { this.isPublishing = false; this.showPublishModal = false; }); }
            };
        });
    });
</script>

<div class="p-8 max-w-5xl mx-auto" x-data="batchManager()">
    {{-- Batch Header --}}
    @include('content-creator.partials.batch-header')

    {{-- Stats Grid --}}
    @include('content-creator.partials.stats-grid')

    {{-- Social Media Feed Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-20">
        @foreach($postsData as $postInfo)
        <div x-data="postCard({{ $postInfo['index'] }})" 
             class="w-full bg-card border border-border rounded-xl shadow-md overflow-visible animate-in fade-in slide-in-from-bottom-4 duration-500 relative" 
             style="animation-delay: {{ $postInfo['index'] * 150 }}ms;">
            
            {{-- Index Badge --}}
            <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-black shadow-lg border-4 border-background z-20">
                {{ $postInfo['index'] + 1 }}
            </div>
            
            {{-- Success Overlay --}}
            @include('content-creator.partials.post-card.success-overlay')
            
            {{-- Hidden File Input --}}
            <input type="file" x-ref="fileInput" class="hidden" accept="image/*,video/*" @change="handleUpload">

            {{-- Post Header --}}
            @include('content-creator.partials.post-card.card-header')

            {{-- Content Body --}}
            @include('content-creator.partials.post-card.content-body')

            {{-- Media Area --}}
            @include('content-creator.partials.post-card.media-area')

            {{-- Actions Footer --}}
            @include('content-creator.partials.post-card.actions-footer')

            {{-- Publish Modal --}}
            @include('content-creator.partials.post-card.modals.publish-modal')

            {{-- Image Creator Modal --}}
            @include('content-creator.partials.post-card.modals.image-creator-modal')
        </div>
        @endforeach
    </div>

    {{-- Delete Batch Confirmation Modal --}}
    @include('content-creator.partials.delete-modal')
</div>
@endif
@endsection
